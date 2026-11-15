#!/usr/bin/env python3
"""
EasyPrint PDF Page Slicer
Extracts specified pages from source PDF and saves as a new PDF.
Usage: python slice_pdf.py <input_pdf> <output_pdf> <page_range>
"""

import sys
import os

try:
    import fitz  # PyMuPDF
except ImportError:
    sys.stderr.write("Error: PyMuPDF (fitz) is not installed.\n")
    sys.exit(1)

def parse_page_indices(page_range_str: str, total_pages: int) -> list:
    """Parse a page range expression into sorted 0-based page indices."""
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

def main():
    if len(sys.argv) < 4:
        sys.stderr.write("Usage: python slice_pdf.py <input_pdf> <output_pdf> <page_range>\n")
        sys.exit(2)

    input_pdf = sys.argv[1]
    output_pdf = sys.argv[2]
    page_range = sys.argv[3]

    if not os.path.isfile(input_pdf):
        sys.stderr.write(f"Error: input file '{input_pdf}' not found.\n")
        sys.exit(3)

    src_doc = fitz.open(input_pdf)
    total_pages = len(src_doc)
    indices = parse_page_indices(page_range, total_pages)

    out_doc = fitz.open()
    for idx in indices:
        out_doc.insert_pdf(src_doc, from_page=idx, to_page=idx)

    os.makedirs(os.path.dirname(os.path.abspath(output_pdf)), exist_ok=True)
    out_doc.save(output_pdf)
    out_doc.close()
    src_doc.close()

    print(f"OK:{len(indices)}")

if __name__ == "__main__":
    main()

