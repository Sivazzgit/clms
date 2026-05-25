<?php
/**
 * CLMS 2.0 — Helpers.php
 * Pure utility functions, no side-effects.
 */

class Helpers
{
    // ----------------------------------------------------------------
    // Code generation — e.g., VND-001, EMP-0042
    // ----------------------------------------------------------------
    public static function generateCode(string $prefix, string $table, string $column): string
    {
        $max = DB::value("SELECT MAX(CAST(SUBSTRING_INDEX(`$column`, '-', -1) AS UNSIGNED)) FROM `$table`");
        $next = (int)($max ?? 0) + 1;
        return strtoupper($prefix) . '-' . str_pad($next, 4, '0', STR_PAD_LEFT);
    }

    // ----------------------------------------------------------------
    // Date helpers
    // ----------------------------------------------------------------
    /** 2026-05-25  → 25-May-2026 */
    public static function dateDisplay(?string $date): string
    {
        if (!$date) return '—';
        return date('d-M-Y', strtotime($date));
    }

    /** Any date string → Y-m-d (SQL) */
    public static function dateSql(?string $date): ?string
    {
        if (!$date) return null;
        return date('Y-m-d', strtotime($date));
    }

    /** Month label: 2026-05 → May 2026 */
    public static function monthLabel(string $ym): string
    {
        return date('F Y', strtotime($ym . '-01'));
    }

    // ----------------------------------------------------------------
    // Pagination
    // ----------------------------------------------------------------
    /**
     * @return array { current, perPage, totalPages, offset, pages[] }
     */
    public static function paginate(int $total, int $perPage = PAGE_SIZE): array
    {
        $current    = max(1, (int)($_GET['page'] ?? 1));
        $totalPages = max(1, (int)ceil($total / $perPage));
        $current    = min($current, $totalPages);
        $offset     = ($current - 1) * $perPage;

        // Page numbers for display (max 7 around current)
        $range  = 3;
        $start  = max(1, $current - $range);
        $end    = min($totalPages, $current + $range);
        $pages  = range($start, $end);

        return compact('current', 'perPage', 'total', 'totalPages', 'offset', 'pages');
    }

    // ----------------------------------------------------------------
    // Sanitisation
    // ----------------------------------------------------------------
    public static function h(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    /** Strip dangerous chars, trim whitespace */
    public static function clean(mixed $value): string
    {
        return trim(strip_tags((string)$value));
    }

    // ----------------------------------------------------------------
    // JSON responses
    // ----------------------------------------------------------------
    public static function jsonOk(mixed $data = null, string $message = ''): never
    {
        header('Content-Type: application/json');
        echo json_encode(['ok' => true, 'message' => $message, 'data' => $data]);
        exit;
    }

    public static function jsonFail(string $message, int $code = 400): never
    {
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode(['ok' => false, 'message' => $message, 'data' => null]);
        exit;
    }

    // ----------------------------------------------------------------
    // Redirect
    // ----------------------------------------------------------------
    public static function redirect(string $url, ?string $flash = null, string $type = 'success'): never
    {
        if ($flash) $_SESSION["flash_$type"] = $flash;
        header("Location: $url");
        exit;
    }

    // ----------------------------------------------------------------
    // File upload
    // ----------------------------------------------------------------
    /**
     * Move an uploaded file to UPLOAD_ROOT/{$subDir}/ with a safe name.
     * @return string relative path from UPLOAD_ROOT on success
     * @throws RuntimeException on failure
     */
    public static function saveUpload(array $file, string $subDir, array $allowedTypes): string
    {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new RuntimeException('File upload failed (error code ' . $file['error'] . ')');
        }
        if ($file['size'] > UPLOAD_MAX_MB * 1024 * 1024) {
            throw new RuntimeException('File exceeds maximum size of ' . UPLOAD_MAX_MB . ' MB');
        }

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime  = $finfo->file($file['tmp_name']);
        if (!in_array($mime, $allowedTypes, true)) {
            throw new RuntimeException('File type not allowed');
        }

        $ext     = pathinfo($file['name'], PATHINFO_EXTENSION);
        $safe    = bin2hex(random_bytes(12)) . '.' . strtolower($ext);
        $destDir = UPLOAD_ROOT . '/' . trim($subDir, '/');
        if (!is_dir($destDir) && !mkdir($destDir, 0750, true)) {
            throw new RuntimeException('Cannot create upload directory');
        }
        $destPath = $destDir . '/' . $safe;
        if (!move_uploaded_file($file['tmp_name'], $destPath)) {
            throw new RuntimeException('Could not move uploaded file');
        }
        return trim($subDir, '/') . '/' . $safe;
    }

    // ----------------------------------------------------------------
    // Formatting
    // ----------------------------------------------------------------
    public static function currency(float $amount): string
    {
        return '₹' . number_format($amount, 2);
    }

    public static function initials(string $name): string
    {
        $words = preg_split('/\s+/', trim($name));
        $chars = array_map(fn($w) => mb_strtoupper(mb_substr($w, 0, 1)), array_slice($words, 0, 2));
        return implode('', $chars);
    }
}
