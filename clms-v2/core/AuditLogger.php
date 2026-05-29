<?php
/**
 * CLMS 2.0 — AuditLogger.php
 * Write a record to audit_log for every significant action.
 */

class AuditLogger
{
    /**
     * @param string      $action    INSERT | UPDATE | DELETE | LOGIN | LOGOUT | APPROVE | REJECT
     * @param string      $module    Module name: users | vendors | employees | indent | ...
     * @param int|null    $recordId  PK of the affected record
     * @param array|null  $oldValues Previous field values (for UPDATE/DELETE)
     * @param array|null  $newValues New field values (INSERT / UPDATE)
     */
    public static function log(
        string  $action,
        string  $module,
        ?int    $recordId  = null,
        ?array  $oldValues = null,
        ?array  $newValues = null
    ): void {
        try {
            $userId    = $_SESSION['user_id']   ?? null;
            $username  = $_SESSION['username']  ?? null;
            $companyId = $_SESSION['company_id'] ?? null;
            $ip        = self::getIp();
            $ua        = substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 500);

            // When impersonating, record the real user who performed the action
            $impersonatedBy = null;
            if (!empty($_SESSION['impersonating_as'])) {
                $impersonatedBy = $_SESSION['real_user_id'] ?? null;
            }

            DB::insert('audit_log', [
                'company_id'      => $companyId,
                'user_id'         => $userId,
                'impersonated_by' => $impersonatedBy,
                'username'        => $username,
                'action'          => strtoupper($action),
                'module'          => $module,
                'record_id'       => $recordId,
                'old_values'      => $oldValues ? json_encode($oldValues) : null,
                'new_values'      => $newValues ? json_encode($newValues) : null,
                'ip_address'      => $ip,
                'user_agent'      => $ua,
            ]);
        } catch (Throwable) {
            // Audit logging must never break the main flow
        }
    }

    /** Remove sensitive fields before logging */
    public static function sanitize(array $data): array
    {
        $sensitive = ['password', 'password_hash', 'aadhaar_no', 'bank_account_no'];
        foreach ($sensitive as $key) {
            if (array_key_exists($key, $data)) {
                $data[$key] = '[REDACTED]';
            }
        }
        return $data;
    }

    private static function getIp(): string
    {
        $candidates = [
            'HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR',
        ];
        foreach ($candidates as $key) {
            $val = $_SERVER[$key] ?? '';
            if ($val) {
                return trim(explode(',', $val)[0]);
            }
        }
        return '0.0.0.0';
    }
}
