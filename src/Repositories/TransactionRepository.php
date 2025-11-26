<?php

namespace App\Repositories;

use App\Database;
use PDO;

class TransactionRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function create(int $walletId, float $amount, string $type, string $description): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO transactions (wallet_id, amount, type, description) 
            VALUES (:wallet_id, :amount, :type, :description)
        ");
        
        $stmt->execute([
            'wallet_id' => $walletId,
            'amount' => $amount,
            'type' => $type,
            'description' => $description
        ]);
        
        return (int) $this->db->lastInsertId();
    }

    public function findByWalletId(int $walletId): array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM transactions 
            WHERE wallet_id = :wallet_id 
            ORDER BY created_at DESC
        ");
        
        $stmt->execute(['wallet_id' => $walletId]);
        return $stmt->fetchAll();
    }

    public function findExpensesByWalletId(int $walletId): array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM transactions 
            WHERE wallet_id = :wallet_id AND type = 'EXPENSE' 
            ORDER BY created_at DESC
        ");
        
        $stmt->execute(['wallet_id' => $walletId]);
        return $stmt->fetchAll();
    }
}

