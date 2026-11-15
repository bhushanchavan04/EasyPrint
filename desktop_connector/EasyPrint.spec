# -*- mode: python ; coding: utf-8 -*-
# PyInstaller spec configuration for EasyPrint Standalone Desktop Spooler (.exe)

block_cipher = None

a = Analysis(
    ['connector.py'],
    pathex=[],
    binaries=[],
    datas=[],
    hiddenimports=[
        'win32print',
        'win32api',
        'win32ui',
        'win32con',
        'win32event',
        'winerror',
        'pystray',
        'pystray._win32',
        'PIL',
        'PIL.Image',
        'PIL.ImageDraw',
        'fitz',
        'requests',
        'tkinter',
        'tkinter.simpledialog',
        'tkinter.messagebox',
        'psutil'
    ],
    hookspath=[],
    hooksconfig={},
    runtime_hooks=[],
    excludes=[],
    win_no_prefer_redirects=False,
    win_private_assemblies=False,
    cipher=block_cipher,
    noarchive=False,
)

pyz = PYZ(a.pure, a.zipped_data, cipher=block_cipher)

exe = EXE(
    pyz,
    a.scripts,
    a.binaries,
    a.zipfiles,
    a.datas,
    [],
    name='EasyPrint',
    debug=False,
    bootloader_ignore_signals=False,
    strip=False,
    upx=True,
    upx_exclude=[],
    runtime_tmpdir=None,
    console=False, # Runs silently without a black CMD window (System Tray UI)
    disable_windowed_traceback=False,
    argv_emulation=False,
    target_arch=None,
    codesign_identity=None,
    entitlements_file=None,
    icon=None
)

