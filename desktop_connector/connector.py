#!/usr/bin/env python3
"""
EasyPrint SaaS — Windows Desktop Print Agent  (connector.py)
Compiled to EasyPrint.exe via PyInstaller.

Architecture:
  • Single-instance mutex (Windows Global mutex)
  • Beautiful dark-theme Tkinter pairing wizard (6-digit PIN)
  • 100% silent GDI printing — NO Windows dialogs, NO extra copies
  • Background polling thread picks up print jobs every 3 s
  • pystray system-tray icon with Re-Pair and Disconnect options
  • Auto-registers with Windows startup on first pair
"""

import os
import sys
import time
import json
import shutil
import tempfile
import threading
import subprocess
import winreg
import requests
import tkinter as tk

# ---------------------------------------------------------------------------
# Optional Windows-only imports
# ---------------------------------------------------------------------------
try:
    import win32print, win32api, win32ui, win32con, win32event, winerror
    import ctypes
    HAVE_WIN32 = True
except ImportError:
    HAVE_WIN32 = False

try:
    from PIL import Image, ImageDraw, ImageWin, ImageOps
    import pystray
    HAVE_TRAY = True
except ImportError:
    HAVE_TRAY = False

try:
    import fitz          # PyMuPDF — silent PDF rasterizer
    HAVE_FITZ = True
except ImportError:
    HAVE_FITZ = False

try:
    import psutil        # used to kill old EasyPrint PIDs safely
    HAVE_PSUTIL = True
except ImportError:
    HAVE_PSUTIL = False

# ---------------------------------------------------------------------------
# Constants
# ---------------------------------------------------------------------------
APP_NAME               = "EasyPrint"
MUTEX_NAME             = "Global\\EasyPrintConnectorMutex"
CONFIG_FILE            = os.path.join(os.path.expanduser("~"), ".easyprint_connector.json")
POLL_INTERVAL_SEC      = 3.0
HEARTBEAT_INTERVAL_SEC = 30.0

VIRTUAL_PRINTER_KEYWORDS = [
    "pdf", "xps", "onenote", "fax", "send to", "cuteftp", "foxit",
    "nitro", "anydesk", "remote desktop", "root print",
]

# ---------------------------------------------------------------------------
# Single-Instance Guard
# ---------------------------------------------------------------------------
_mutex_handle = None

def ensure_single_instance():
    """
    Acquire a named Windows mutex so only one EasyPrint runs at a time.
    If a second instance starts, the user is offered:
      YES  → kill the OLD instance (by PID) and continue startup
      NO   → exit quietly, leaving the existing instance untouched
    """
    global _mutex_handle
    if not HAVE_WIN32:
        return

    _mutex_handle = win32event.CreateMutex(None, False, MUTEX_NAME)
    if win32api.GetLastError() != winerror.ERROR_ALREADY_EXISTS:
        return  # Only instance — carry on

    # ---- A previous instance holds the mutex ----
    MB_YESNO      = 0x04
    MB_ICONWARN   = 0x30
    MB_TOPMOST    = 0x00040000
    IDYES         = 6

    try:
        choice = ctypes.windll.user32.MessageBoxW(
            0,
            "EasyPrint is already running in your Windows System Tray.\n\n"
            "• Click YES to Force Restart\n"
            "  (closes old instance — use this to enter a new PIN)\n\n"
            "• Click NO to cancel and keep the existing instance.",
            "EasyPrint Already Running",
            MB_YESNO | MB_ICONWARN | MB_TOPMOST,
        )
    except Exception:
        sys.exit(0)

    if choice != IDYES:
        sys.exit(0)

    # ---- User chose YES: kill only the OTHER EasyPrint PIDs ----
    current_pid = os.getpid()

    if HAVE_PSUTIL:
        for proc in psutil.process_iter(["pid", "name"]):
            try:
                name = proc.info.get("name") or ""
                pid  = proc.info.get("pid", -1)
                if "EasyPrint" in name and pid != current_pid:
                    proc.kill()
            except Exception:
                pass
    else:
        # Fallback: wmic with PID filter (never kills self)
        try:
            subprocess.run(
                ["wmic", "process", "where",
                 f'(name="EasyPrint.exe" and not ProcessId={current_pid})',
                 "call", "terminate"],
                capture_output=True, timeout=8,
            )
        except Exception:
            pass

    time.sleep(1.5)   # Wait for the old mutex to be released by OS

    # Re-acquire mutex for ourselves
    try:
        win32api.CloseHandle(_mutex_handle)
    except Exception:
        pass
    _mutex_handle = win32event.CreateMutex(None, False, MUTEX_NAME)
    # Startup continues normally below


# ---------------------------------------------------------------------------
# Silent GDI Image Printer (NO Windows Print Pictures dialog)
# ---------------------------------------------------------------------------
def print_image_gdi(file_path: str, printer_name: str, copies: int = 1) -> bool:
    """
    Sends an image file directly to the printer's GDI Device Context.
    100% silent — no dialogs, no ShellExecute, no extra copies.
    Auto-rotates if image orientation doesn't match page orientation.
    """
    if not HAVE_WIN32:
        return False
    try:
        img = Image.open(file_path)
        img = ImageOps.exif_transpose(img)
        img = img.convert("RGB")

        hDC = win32ui.CreateDC()
        hDC.CreatePrinterDC(printer_name)
        try:
            pw = hDC.GetDeviceCaps(win32con.HORZRES)
            ph = hDC.GetDeviceCaps(win32con.VERTRES)
            iw, ih = img.size

            # Rotate image to match page orientation
            if (ph >= pw) != (ih >= iw):
                img = img.rotate(90, expand=True)
                iw, ih = img.size

            scale  = min(pw / iw, ph / ih)
            dw, dh = int(iw * scale), int(ih * scale)
            x, y   = (pw - dw) // 2, (ph - dh) // 2

            dib      = ImageWin.Dib(img)
            job_name = f"EasyPrint_{os.path.basename(file_path)}"

            for _ in range(copies):
                hDC.StartDoc(job_name)
                hDC.StartPage()
                dib.draw(hDC.GetHandleOutput(), (x, y, x + dw, y + dh))
                hDC.EndPage()
                hDC.EndDoc()
            return True
        finally:
            hDC.DeleteDC()
    except Exception as e:
        print(f"[GDI-IMAGE ERROR] {e}")
        return False


# ---------------------------------------------------------------------------
# Silent GDI PDF Printer (PyMuPDF → 300 DPI → printer DC)
# ---------------------------------------------------------------------------
def parse_page_indices(page_range_str: str, total_pages: int) -> list:
    """
    Parse a page range expression (e.g. 'all', 'odd', 'even', '1-5, 8, 11-13')
    into a sorted list of 0-based page indices bounded by total_pages.
    """
    if not page_range_str or str(page_range_str).strip().lower() == 'all':
        return list(range(total_pages))

    s = str(page_range_str).strip().lower()
    if s in ('odd', 'odd pages only'):
        return [i for i in range(total_pages) if (i + 1) % 2 != 0]

    if s in ('even', 'even pages only'):
        return [i for i in range(total_pages) if (i + 1) % 2 == 0]

    selected = set()
    parts = s.split(',')
    for part in parts:
        part = part.strip()
        if not part:
            continue
        if '-' in part:
            bounds = part.split('-', 1)
            try:
                start = max(1, int(bounds[0].strip()))
                end = min(total_pages, int(bounds[1].strip()))
                if start <= end:
                    for p in range(start, end + 1):
                        selected.add(p - 1)
            except ValueError:
                pass
        else:
            try:
                p = int(part)
                if 1 <= p <= total_pages:
                    selected.add(p - 1)
            except ValueError:
                pass

    if not selected:
        return list(range(total_pages))
    return sorted(list(selected))


def print_pdf_gdi(file_path: str, printer_name: str, copies: int = 1, page_range: str = 'all', layout: str = 'PORTRAIT') -> bool:
    """
    Rasterises specified PDF pages at 300 DPI and sends directly to the printer DC.
    Silent, no dialogs, correct orientation, correct copy count.
    """
    if not (HAVE_WIN32 and HAVE_FITZ):
        return False
    try:
        doc = fitz.open(file_path)
        hDC = win32ui.CreateDC()
        hDC.CreatePrinterDC(printer_name)
        try:
            pw = hDC.GetDeviceCaps(win32con.HORZRES)
            ph = hDC.GetDeviceCaps(win32con.VERTRES)
            job_name = f"EasyPrint_{os.path.basename(file_path)}"

            target_indices = parse_page_indices(page_range, len(doc))
            is_landscape_req = (str(layout).upper() == 'LANDSCAPE')

            for _ in range(copies):
                hDC.StartDoc(job_name)
                for page_idx in target_indices:
                    page = doc[page_idx]
                    pix = page.get_pixmap(dpi=300)
                    img = Image.frombytes("RGB", [pix.width, pix.height], pix.samples)
                    iw, ih = img.size

                    if is_landscape_req:
                        if ih > iw:
                            img = img.rotate(90, expand=True)
                            iw, ih = img.size
                    else:
                        if (ph >= pw) != (ih >= iw):
                            img = img.rotate(90, expand=True)
                            iw, ih = img.size

                    scale  = min(pw / iw, ph / ih)
                    dw, dh = int(iw * scale), int(ih * scale)
                    x, y   = (pw - dw) // 2, (ph - dh) // 2

                    dib = ImageWin.Dib(img)
                    hDC.StartPage()
                    dib.draw(hDC.GetHandleOutput(), (x, y, x + dw, y + dh))
                    hDC.EndPage()
                hDC.EndDoc()
            return True
        finally:
            hDC.DeleteDC()
            doc.close()
    except Exception as e:
        print(f"[GDI-PDF ERROR] {e}")
        return False


# ---------------------------------------------------------------------------
# Main Connector Class
# ---------------------------------------------------------------------------
class EasyPrintConnector:

    def __init__(self):
        self.config          = self._load_config()
        self.server_url      = self.config.get("server_url", "http://localhost:8000")
        self.auth_token      = self.config.get("auth_token", "")
        self.shop_name       = self.config.get("shop_name", "EasyPrint Shop")
        self.default_printer = None
        self.tray_icon       = None
        self.running         = True

    # ------------------------------------------------------------------
    # Config helpers
    # ------------------------------------------------------------------
    def _load_config(self) -> dict:
        if os.path.exists(CONFIG_FILE):
            try:
                with open(CONFIG_FILE, "r", encoding="utf-8") as fh:
                    return json.load(fh)
            except Exception:
                pass
        return {}

    def _save_config(self):
        try:
            with open(CONFIG_FILE, "w", encoding="utf-8") as fh:
                json.dump(self.config, fh, indent=4)
        except Exception as e:
            print(f"[CONFIG] Save error: {e}")

    # ------------------------------------------------------------------
    # Pairing Wizard  (dark-theme Tkinter)
    # ------------------------------------------------------------------
    def run_pairing_wizard(self, icon=None, item=None):
        """
        Single-window dark-themed pairing dialog.
        Fields: Server URL + 6-digit PIN
        On success: saves config, registers Windows startup, destroys window.
        """
        root = tk.Tk()
        root.title("EasyPrint — Connect to Shop")
        root.resizable(False, False)
        root.configure(bg="#1e293b")

        W, H = 480, 400
        sw, sh = root.winfo_screenwidth(), root.winfo_screenheight()
        root.geometry(f"{W}x{H}+{(sw-W)//2}+{(sh-H)//2}")
        root.lift()
        root.attributes("-topmost", True)

        # Header bar
        hdr = tk.Frame(root, bg="#6366f1", pady=20)
        hdr.pack(fill="x")
        tk.Label(hdr, text="🖨  EasyPrint Connector Setup",
                 bg="#6366f1", fg="white",
                 font=("Segoe UI", 15, "bold")).pack()
        tk.Label(hdr, text="Connect this Windows PC to your EasyPrint merchant account",
                 bg="#6366f1", fg="#c7d2fe",
                 font=("Segoe UI", 9)).pack()

        body = tk.Frame(root, bg="#1e293b", padx=32, pady=24)
        body.pack(fill="both", expand=True)

        def lbl(text):
            tk.Label(body, text=text, bg="#1e293b", fg="#94a3b8",
                     font=("Segoe UI", 9, "bold"), anchor="w").pack(fill="x")

        def entry(var, **kw):
            e = tk.Entry(body, textvariable=var,
                         bg="#0f172a", fg="#e2e8f0",
                         insertbackground="white", relief="flat", bd=0,
                         highlightthickness=1,
                         highlightbackground="#334155",
                         highlightcolor="#6366f1", **kw)
            e.pack(fill="x", ipady=8, pady=(4, 16))
            return e

        # Server URL
        lbl("EasyPrint Server URL")
        url_var = tk.StringVar(value=self.server_url or "http://localhost:8000")
        entry(url_var, font=("Consolas", 10))

        # PIN
        lbl("6-Digit Pairing PIN  (Dashboard → Printers → Pair New PC)")
        pin_var = tk.StringVar()
        pin_entry = tk.Entry(body, textvariable=pin_var,
                             bg="#0f172a", fg="#6366f1",
                             insertbackground="#6366f1",
                             font=("Consolas", 22, "bold"),
                             justify="center", relief="flat", bd=0,
                             highlightthickness=2,
                             highlightbackground="#334155",
                             highlightcolor="#6366f1")
        pin_entry.pack(fill="x", ipady=12, pady=(4, 4))
        pin_entry.focus_set()

        tk.Label(body, text="PIN is valid for 15 minutes after generation.",
                 bg="#1e293b", fg="#475569",
                 font=("Segoe UI", 8), anchor="w").pack(fill="x")

        # Status line
        status_var = tk.StringVar()
        tk.Label(body, textvariable=status_var,
                 bg="#1e293b", fg="#f59e0b",
                 font=("Segoe UI", 9, "bold")).pack(pady=(12, 0))

        paired = {"ok": False}

        def do_connect():
            url = url_var.get().strip().rstrip("/")
            raw_pin = pin_var.get().strip()
            pin = "".join(c for c in raw_pin if c.isdigit())

            if not url:
                status_var.set("⚠  Please enter the server URL.")
                return
            if len(pin) != 6:
                status_var.set(f"⚠  Need 6 digits — you entered {len(pin)}.")
                return

            status_var.set("⏳  Connecting to server…")
            btn.config(state="disabled")
            root.update()

            self.server_url = url
            self.config["server_url"] = url
            device_name = os.environ.get("COMPUTERNAME", "Counter-PC")

            try:
                resp = requests.post(
                    f"{url}/api/connector/pair",
                    json={"pairing_code": pin, "device_name": device_name},
                    timeout=10,
                )
                data = resp.json()

                if resp.status_code == 200 and data.get("success"):
                    self.auth_token       = data["auth_token"]
                    self.shop_name        = data.get("shop_name", "Shop")
                    self.config["auth_token"]  = self.auth_token
                    self.config["shop_name"]   = self.shop_name
                    self._save_config()
                    self._register_startup()
                    paired["ok"] = True
                    status_var.set(f"✅  Connected to '{self.shop_name}'!")
                    root.update()
                    root.after(1500, root.destroy)
                else:
                    err = data.get("error", "Invalid or expired PIN.")
                    status_var.set(f"❌  {err}")
                    btn.config(state="normal")
            except requests.exceptions.ConnectionError:
                status_var.set("❌  Cannot reach server. Check the URL.")
                btn.config(state="normal")
            except Exception as exc:
                status_var.set(f"❌  Error: {exc}")
                btn.config(state="normal")

        btn = tk.Button(body, text="Connect & Activate Spooler",
                        command=do_connect, cursor="hand2",
                        bg="#6366f1", fg="white",
                        font=("Segoe UI", 11, "bold"),
                        activebackground="#4f46e5", activeforeground="white",
                        relief="flat", bd=0, pady=10)
        btn.pack(fill="x", pady=(14, 0))
        root.bind("<Return>", lambda _e: do_connect())
        root.mainloop()

        if not paired["ok"]:
            # User closed the window without pairing — exit
            sys.exit(1)

    # ------------------------------------------------------------------
    # Printer discovery
    # ------------------------------------------------------------------
    def _get_physical_printers(self) -> list:
        printers = []
        if not HAVE_WIN32:
            return [{"name": "Default Printer", "color": False, "duplex": False}]

        try:
            flags   = win32print.PRINTER_ENUM_LOCAL | win32print.PRINTER_ENUM_CONNECTIONS
            default = ""
            try:
                default = win32print.GetDefaultPrinter()
            except Exception:
                pass

            for item in win32print.EnumPrinters(flags):
                name  = item[2]
                lower = name.lower()
                if any(v in lower for v in VIRTUAL_PRINTER_KEYWORDS):
                    continue
                is_default = (name == default)
                if is_default:
                    self.default_printer = name
                printers.append({
                    "name": name, "default": is_default,
                    "color": False, "duplex": False,
                    "paper_sizes": ["A4", "LETTER", "LEGAL"],
                })

            if not self.default_printer and printers:
                self.default_printer = printers[0]["name"]
        except Exception as e:
            print(f"[PRINTER ENUM] {e}")

        if not printers:
            # Hard fallback so the app doesn't crash on unusual systems
            printers.append({"name": "Generic Printer", "color": False, "duplex": False})
            self.default_printer = "Generic Printer"

        return printers

    def _sync_printers(self):
        printers = self._get_physical_printers()
        try:
            resp = requests.post(
                f"{self.server_url}/api/connector/register-printers",
                headers=self._auth_headers(),
                json={"printers": printers},
                timeout=10,
            )
            if resp.status_code == 200:
                print(f"[SYNC] Registered {len(printers)} printer(s) with server.")
        except Exception as e:
            print(f"[SYNC] Printer registration failed: {e}")

    # ------------------------------------------------------------------
    # Multi-engine silent spooler
    # ------------------------------------------------------------------
    def _spool(self, file_path: str, target_printer: str, settings: dict) -> bool:
        # Resolve best available printer name
        printer = target_printer or self.default_printer
        if not printer or printer.lower() in ("hp laserjet pro mfp m126nw", "generic printer"):
            if HAVE_WIN32:
                try:
                    printer = win32print.GetDefaultPrinter()
                except Exception:
                    pass
        if not printer:
            printer = self.default_printer or "Canon LBP2900"

        copies     = max(1, int(settings.get("copies", 1)))
        page_range = str(settings.get("page_range", "all")).strip()
        layout     = str(settings.get("layout", "PORTRAIT")).strip().upper()
        ext        = os.path.splitext(file_path)[1].lower()

        print(f"[SPOOLER] printer='{printer}' copies={copies} pages='{page_range}' layout='{layout}' file='{os.path.basename(file_path)}'")

        # ── Engine A: Images → silent GDI (never shows any Windows dialog) ──
        if ext in (".png", ".jpg", ".jpeg", ".bmp", ".webp", ".gif", ".tiff"):
            print(f"[ENGINE A] GDI image → {printer}")
            if print_image_gdi(file_path, printer, copies):
                return True
            print("[ENGINE A] Failed — no fallback for images to avoid dialogs.")
            return False

        # ── Engine B: PDF ──
        if ext == ".pdf":
            file_to_spool = file_path
            temp_sliced_file = None

            # Physical PDF Page Slicing: If specific pages are requested,
            # extract ONLY those pages into a new physical PDF file first.
            # This guarantees that the printer CANNOT print any unselected pages!
            if HAVE_FITZ and page_range and page_range.lower() != 'all':
                try:
                    src_doc = fitz.open(file_path)
                    total_p = len(src_doc)
                    target_indices = parse_page_indices(page_range, total_p)
                    if target_indices and len(target_indices) < total_p:
                        sliced_doc = fitz.open()
                        for p_idx in target_indices:
                            sliced_doc.insert_pdf(src_doc, from_page=p_idx, to_page=p_idx)
                        temp_sliced_file = os.path.join(tempfile.gettempdir(), f"sliced_{int(time.time())}_{os.path.basename(file_path)}")
                        sliced_doc.save(temp_sliced_file)
                        sliced_doc.close()
                        file_to_spool = temp_sliced_file
                        print(f"[PDF SLICER] Extracted {len(target_indices)} page(s) ({page_range}) out of {total_p} into {file_to_spool}")
                    src_doc.close()
                except Exception as e:
                    print(f"[PDF SLICER ERROR]: {e}")

            try:
                # B1: SumatraPDF (silent, fast, vector quality)
                sumatra = self._find_sumatra()
                if sumatra:
                    cmd = (f'"{sumatra}" -print-to "{printer}" '
                           f'-print-settings "{copies}x" -silent "{file_to_spool}"')
                    print(f"[ENGINE B1] SumatraPDF: {cmd}")
                    try:
                        r = subprocess.run(cmd, shell=True, timeout=45)
                        if r.returncode == 0:
                            return True
                    except Exception as e:
                        print(f"[ENGINE B1] Failed: {e}")

                # B2: PyMuPDF GDI (rasterised 300 DPI, always works)
                if HAVE_FITZ and HAVE_WIN32:
                    print(f"[ENGINE B2] PyMuPDF GDI → {printer} (pages: {page_range}, {copies} copies)...")
                    if print_pdf_gdi(file_to_spool, printer, copies, 'all', layout):
                        return True
                    print("[ENGINE B2] Failed.")
            finally:
                if temp_sliced_file and os.path.exists(temp_sliced_file):
                    try:
                        os.remove(temp_sliced_file)
                    except Exception:
                        pass

        # ── Engine C: Everything else (DOCX, ODT…) → ShellExecute printto ──
        if HAVE_WIN32:
            print(f"[ENGINE C] ShellExecute printto → {printer}")
            try:
                for _ in range(copies):
                    win32api.ShellExecute(0, "printto", file_path, f'"{printer}"', ".", 0)
                    time.sleep(1.2)
                return True
            except Exception as e:
                print(f"[ENGINE C] Failed: {e}")

        return False

    @staticmethod
    def _find_sumatra() -> str | None:
        candidates = [
            os.path.join(os.path.dirname(sys.executable), "SumatraPDF.exe"),
            r"C:\Program Files\SumatraPDF\SumatraPDF.exe",
            r"C:\Program Files (x86)\SumatraPDF\SumatraPDF.exe",
            "SumatraPDF.exe",
        ]
        for p in candidates:
            if os.path.exists(p) or shutil.which(p):
                return p
        return None

    # ------------------------------------------------------------------
    # Job Polling Worker (background thread)
    # ------------------------------------------------------------------
    def _auth_headers(self) -> dict:
        return {"Authorization": f"Bearer {self.auth_token}"}

    def _worker_loop(self):
        while self.running:
            try:
                resp = requests.get(
                    f"{self.server_url}/api/connector/jobs",
                    headers=self._auth_headers(),
                    timeout=10,
                )
                if resp.status_code == 402:
                    print("[PAYWALL] Subscription expired. Waiting…")
                    time.sleep(30)
                    continue
                if resp.status_code == 200:
                    data = resp.json()
                    if data.get("has_job"):
                        self._process_job(data)
            except Exception:
                pass   # Network blip — retry on next tick
            time.sleep(POLL_INTERVAL_SEC)

    def _process_job(self, job: dict):
        job_id      = job["job_id"]
        order_num   = job["order_number"]
        dl_url      = job["file_download_url"]
        file_name   = job["file_name"]
        printer     = job.get("target_printer", "")
        settings    = job.get("settings", {})

        print(f"[JOB] Order {order_num}  job_id={job_id}")
        self._update_status(job_id, "DOWNLOADING")

        tmp = os.path.join(tempfile.gettempdir(), f"ep_{job_id}_{file_name}")
        try:
            with requests.get(dl_url, stream=True, timeout=60) as r:
                r.raise_for_status()
                with open(tmp, "wb") as fh:
                    for chunk in r.iter_content(8192):
                        fh.write(chunk)

            ok = self._spool(tmp, printer, settings)
            self._update_status(job_id, "COMPLETED" if ok else "FAILED")
            print(f"[JOB] {'✓ COMPLETED' if ok else '✗ FAILED'}  {order_num}")

        except Exception as exc:
            print(f"[JOB] Exception: {exc}")
            self._update_status(job_id, "FAILED")
        finally:
            try:
                if os.path.exists(tmp):
                    os.remove(tmp)
            except Exception:
                pass

    def _update_status(self, job_id: str, status: str):
        try:
            requests.post(
                f"{self.server_url}/api/connector/jobs/{job_id}/status",
                headers=self._auth_headers(),
                json={"status": status},
                timeout=10,
            )
        except Exception:
            pass

    # ------------------------------------------------------------------
    # Windows Startup Registration
    # ------------------------------------------------------------------
    def _register_startup(self):
        try:
            exe = sys.executable if getattr(sys, "frozen", False) else os.path.abspath(__file__)
            key = winreg.OpenKey(
                winreg.HKEY_CURRENT_USER,
                r"Software\Microsoft\Windows\CurrentVersion\Run",
                0, winreg.KEY_SET_VALUE,
            )
            winreg.SetValueEx(key, APP_NAME, 0, winreg.REG_SZ, f'"{exe}"')
            winreg.CloseKey(key)
            print("[STARTUP] Registered in Windows startup.")
        except Exception as e:
            print(f"[STARTUP] Failed: {e}")

    # ------------------------------------------------------------------
    # Tray Icon helpers
    # ------------------------------------------------------------------
    def _make_tray_icon(self) -> "Image.Image":
        img = Image.new("RGB", (64, 64), (30, 41, 59))
        d   = ImageDraw.Draw(img)
        d.rectangle([14, 20, 50, 48], fill=(99, 102, 241))   # printer body
        d.rectangle([20, 10, 44, 20], fill=(255, 255, 255))  # paper in
        d.rectangle([20, 40, 44, 54], fill=(16, 185, 129))   # paper out
        return img

    def disconnect_and_reset(self, icon=None, item=None):
        """Clear local config and exit — next launch shows the pairing wizard."""
        try:
            if os.path.exists(CONFIG_FILE):
                os.remove(CONFIG_FILE)
        except Exception:
            pass
        if HAVE_WIN32:
            try:
                ctypes.windll.user32.MessageBoxW(
                    0,
                    "EasyPrint has been disconnected from your shop.\n\n"
                    "Next time you launch EasyPrint you can pair with a new PIN.",
                    "EasyPrint Disconnected",
                    0x40 | 0x10000,
                )
            except Exception:
                pass
        self.exit_app()

    def exit_app(self, icon=None, item=None):
        self.running = False
        if self.tray_icon:
            try:
                self.tray_icon.stop()
            except Exception:
                pass
        sys.exit(0)

    # ------------------------------------------------------------------
    # Entry Point
    # ------------------------------------------------------------------
    def start(self):
        # 1. Pairing wizard if not configured
        if not self.auth_token:
            self.run_pairing_wizard()

        # 2. Reload config in case the wizard just wrote it
        self.config      = self._load_config()
        self.server_url  = self.config.get("server_url", self.server_url)
        self.auth_token  = self.config.get("auth_token", self.auth_token)
        self.shop_name   = self.config.get("shop_name", self.shop_name)

        # 3. Sync installed printers with server
        self._sync_printers()

        # 4. Start background polling worker
        t = threading.Thread(target=self._worker_loop, daemon=True)
        t.start()

        # 5. Run System Tray (blocking call — keeps app alive)
        if HAVE_TRAY:
            menu = pystray.Menu(
                pystray.MenuItem(f"EasyPrint: {self.shop_name}", None, enabled=False),
                pystray.MenuItem("● Active & Spooling",          None, enabled=False),
                pystray.Menu.SEPARATOR,
                pystray.MenuItem("Re-Pair with PIN",    self.run_pairing_wizard),
                pystray.MenuItem("Disconnect & Unpair", self.disconnect_and_reset),
                pystray.Menu.SEPARATOR,
                pystray.MenuItem("Exit",                self.exit_app),
            )
            self.tray_icon = pystray.Icon(
                APP_NAME,
                self._make_tray_icon(),
                f"{APP_NAME} Spooler — {self.shop_name}",
                menu,
            )
            self.tray_icon.run()
        else:
            while self.running:
                time.sleep(1)


# ---------------------------------------------------------------------------
# Entry-point
# ---------------------------------------------------------------------------
if __name__ == "__main__":
    ensure_single_instance()
    connector = EasyPrintConnector()
    connector.start()
