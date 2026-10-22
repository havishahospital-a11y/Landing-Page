<?php
declare(strict_types=1);
function database(): PDO
{
    return new PDO('mysql:host=' . env('DB_HOST', '127.0.0.1') . ';port=' . env('DB_PORT', '3306') . ';dbname=' . env('DB_NAME', 'havisha') . ';charset=utf8mb4', env('DB_USER'), env('DB_PASSWORD'), [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_EMULATE_PREPARES => false, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]);
}
final class Repository
{
    public function __construct(private PDO $db)
    {
    }
    public function limit(string $ip): bool
    {
        $bucket = hash_hmac('sha256', $ip . '|' . floor(time() / 900), env('APP_KEY', 'local-only-key'));
        $q = $this->db->prepare('INSERT INTO rate_limits (bucket,hits,expires_at) VALUES (?,1,DATE_ADD(UTC_TIMESTAMP(),INTERVAL 30 MINUTE)) ON DUPLICATE KEY UPDATE hits=hits+1');
        $q->execute([$bucket]);
        $q = $this->db->prepare('SELECT hits FROM rate_limits WHERE bucket=?');
        $q->execute([$bucket]);
        return (int) $q->fetchColumn() <= 10;
    }
    public function find(string $key): ?array
    {
        $q = $this->db->prepare('SELECT public_reference,name,phone,branch,symptoms,other_symptom,city,preferred_date,landing_page,attribution FROM appointments WHERE idempotency_key=?');
        $q->execute([$key]);
        return $q->fetch() ?: null;
    }
    public function save(array $d, string $key, string $origin, array $attribution): array
    {
        if ($existing = $this->find($key))
            return $existing;
        $reference = bin2hex(random_bytes(16));
        $q = $this->db->prepare('INSERT INTO appointments (public_reference,idempotency_key,name,phone,branch,symptoms,other_symptom,city,preferred_date,landing_page,attribution,consent_at,created_at,updated_at) VALUES (?,?,?,?,?,?,?,?,?,?,?,UTC_TIMESTAMP(),UTC_TIMESTAMP(),UTC_TIMESTAMP())');
        try {
            $q->execute([$reference, $key, $d['name'], $d['phone'], $d['branch'], json_encode($d['symptoms'], JSON_THROW_ON_ERROR), $d['other'] ?: null, $d['city'], $d['date'], $origin, json_encode($attribution, JSON_THROW_ON_ERROR)]);
        } catch (PDOException $ex) {
            if ($ex->getCode() === '23000' && ($existing = $this->find($key)))
                return $existing;
            throw $ex;
        }
        return $this->find($key) ?? throw new RuntimeException('Saved appointment could not be loaded.');
    }
}
