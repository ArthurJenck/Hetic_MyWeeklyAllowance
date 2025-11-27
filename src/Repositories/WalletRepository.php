<?php

namespace App\Repositories;

use App\Database;
use PDO;

class WalletRepository
{
    private PDO $db;

    public function __construct(?PDO $db = null)
    {
        $this->db = $db ?? Database::getInstance()->getConnection();
    }

    public function create(int $userId, float $weeklyAllowance = 0.00): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO wallets (user_id, balance, weekly_allowance, weekly_remaining) 
            VALUES (:user_id, 0.00, :weekly_allowance, :weekly_remaining)
        ");

        $stmt->execute([
            'user_id' => $userId,
            'weekly_allowance' => $weeklyAllowance,
            'weekly_remaining' => $weeklyAllowance
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function findByUserId(int $userId): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM wallets WHERE user_id = :user_id");
        $stmt->execute(['user_id' => $userId]);
        $wallet = $stmt->fetch();

        return $wallet ?: null;
    }

    public function updateBalance(int $walletId, float $balance): bool
    {
        $stmt = $this->db->prepare("UPDATE wallets SET balance = :balance WHERE id = :id");
        return $stmt->execute(['id' => $walletId, 'balance' => $balance]);
    }

    public function updateWeeklyRemaining(int $walletId, float $remaining): bool
    {
        $stmt = $this->db->prepare("UPDATE wallets SET weekly_remaining = :remaining WHERE id = :id");
        return $stmt->execute(['id' => $walletId, 'remaining' => $remaining]);
    }

    public function setWeeklyAllowance(int $walletId, float $allowance): bool
    {
        $stmt = $this->db->prepare("
            UPDATE wallets 
            SET weekly_allowance = :allowance, weekly_remaining = :allowance 
            WHERE id = :id
        ");

        return $stmt->execute(['id' => $walletId, 'allowance' => $allowance]);
    }

    public function resetWeeklyAllowance(int $walletId): bool
    {
        $stmt = $this->db->prepare("
            UPDATE wallets 
            SET weekly_remaining = weekly_allowance, 
                last_reset_date = :reset_date,
                balance = balance + weekly_allowance
            WHERE id = :id
        ");

        return $stmt->execute([
            'id' => $walletId,
            'reset_date' => date('Y-m-d')
        ]);
    }
}
