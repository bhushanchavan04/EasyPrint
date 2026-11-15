<?php

namespace App\Services;

class DocumentParser
{
    /**
     * Inspect file and return exact page count
     */
    public static function getPageCount(string $filePath, string $mimeType): int
    {
        if (!file_exists($filePath)) {
            return 1;
        }

        // Image files are single-page documents
        if (str_starts_with($mimeType, 'image/')) {
            return 1;
        }

        // PDF Page counting
        if ($mimeType === 'application/pdf' || str_ends_with(strtolower($filePath), '.pdf')) {
            return self::countPdfPages($filePath);
        }

        // Word DOCX Page counting
        if (str_contains($mimeType, 'wordprocessingml') || str_ends_with(strtolower($filePath), '.docx')) {
            return self::countDocxPages($filePath);
        }

        return 1;
    }

    /**
     * Robust pure PHP PDF page counting algorithm
     */
    private static function countPdfPages(string $path): int
    {
        $handle = @fopen($path, 'rb');
        if (!$handle) {
            return 1;
        }

        $content = fread($handle, 409600); // Read first chunk
        fclose($handle);

        // Pattern 1: Look for /Count in Document Catalog Pages object
        if (preg_match('/\/Type\s*\/Pages[^>]*\/Count\s+(\d+)/is', $content, $matches)) {
            return max(1, (int)$matches[1]);
        }

        // Pattern 2: Scan entire file in chunks for "/Type /Page" tokens (excluding Pages)
        $fileContent = @file_get_contents($path);
        if ($fileContent !== false) {
            // Count /Type /Page occurrences (ensuring not /Type /Pages)
            $count = preg_match_all('/\/Type\s*\/Page\b/i', $fileContent, $matches);
            if ($count > 0) {
                return (int)$count;
            }
        }

        return 1;
    }

    /**
     * DOCX Page counting by parsing docProps/app.xml
     */
    private static function countDocxPages(string $path): int
    {
        if (!class_exists('ZipArchive')) {
            return 1;
        }

        $zip = new \ZipArchive();
        if ($zip->open($path) === true) {
            $xml = $zip->getFromName('docProps/app.xml');
            $zip->close();

            if ($xml && preg_match('/<Pages>(\d+)<\/Pages>/i', $xml, $matches)) {
                return max(1, (int)$matches[1]);
            }
        }

        return 1;
    }

    /**
     * Parse a page range string (e.g. 'all', 'odd', 'even', '1-5, 8, 11-13')
     * into an array of 1-based unique page numbers bounded by $totalPages.
     *
     * @return int[]
     */
    public static function parsePageRange(string $rangeStr, int $totalPages): array
    {
        $totalPages = max(1, $totalPages);
        $rangeStr = strtolower(trim($rangeStr));

        if ($rangeStr === '' || $rangeStr === 'all') {
            return range(1, $totalPages);
        }

        if ($rangeStr === 'odd' || $rangeStr === 'odd pages only') {
            $pages = [];
            for ($i = 1; $i <= $totalPages; $i += 2) {
                $pages[] = $i;
            }
            return $pages;
        }

        if ($rangeStr === 'even' || $rangeStr === 'even pages only') {
            $pages = [];
            for ($i = 2; $i <= $totalPages; $i += 2) {
                $pages[] = $i;
            }
            return $pages;
        }

        // Custom comma-separated list of single pages or ranges
        $selected = [];
        $parts = explode(',', $rangeStr);
        foreach ($parts as $part) {
            $part = trim($part);
            if ($part === '') {
                continue;
            }

            if (str_contains($part, '-')) {
                $bounds = explode('-', $part, 2);
                $start = (int)trim($bounds[0]);
                $end = (int)trim($bounds[1]);
                if ($start > 0 && $end >= $start) {
                    $startClamped = max(1, $start);
                    $endClamped = min($totalPages, $end);
                    for ($p = $startClamped; $p <= $endClamped; $p++) {
                        $selected[$p] = true;
                    }
                }
            } else {
                $p = (int)$part;
                if ($p >= 1 && $p <= $totalPages) {
                    $selected[$p] = true;
                }
            }
        }

        if (empty($selected)) {
            return range(1, $totalPages);
        }

        $result = array_keys($selected);
        sort($result, SORT_NUMERIC);
        return $result;
    }

    /**
     * Slice PDF file on disk to only include requested pages.
     * Returns the sliced file path, or the original if slicing is not needed / fails.
     */
    public static function slicePdf(string $sourcePdfPath, string $pageRange, int $totalPages): string
    {
        if (!file_exists($sourcePdfPath) || $totalPages <= 1) {
            return $sourcePdfPath;
        }

        $pageRange = trim($pageRange);
        if ($pageRange === '' || strtolower($pageRange) === 'all') {
            return $sourcePdfPath;
        }

        $pages = self::parsePageRange($pageRange, $totalPages);
        if (count($pages) === $totalPages) {
            return $sourcePdfPath;
        }

        // Prepare destination directory
        $sliceDir = __DIR__ . '/../../public/uploads/documents/sliced';
        if (!is_dir($sliceDir)) {
            @mkdir($sliceDir, 0777, true);
        }

        $hash = md5($sourcePdfPath . '|' . $pageRange . '|' . filemtime($sourcePdfPath));
        $targetFile = $sliceDir . '/slice_' . $hash . '.pdf';

        if (file_exists($targetFile) && filesize($targetFile) > 0) {
            return $targetFile;
        }

        $scriptPath = realpath(__DIR__ . '/../../scripts/slice_pdf.py');
        if (!$scriptPath || !file_exists($scriptPath)) {
            return $sourcePdfPath;
        }

        $cmd = 'python ' . escapeshellarg($scriptPath) . ' ' .
               escapeshellarg($sourcePdfPath) . ' ' .
               escapeshellarg($targetFile) . ' ' .
               escapeshellarg($pageRange) . ' 2>&1';

        $output = shell_exec($cmd);
        if (file_exists($targetFile) && filesize($targetFile) > 0) {
            return $targetFile;
        }

        return $sourcePdfPath;
    }
}

