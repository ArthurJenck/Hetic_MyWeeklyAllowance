<?php

namespace App\Repositories;

use App\Database;
use App\Models\ParentUser;
use App\Models\Teenager;
use PDO;

class UserRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function createParent(string $name, string $email, string $password): int
    {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $this->db->prepare("
            INSERT INTO users (name, email, password, role) 
            VALUES (:name, :email, :password, 'parent')
        ");
        
        $stmt->execute([
            'name' => $name,
            'email' => $email,
            'password' => $hashedPassword
        ]);
        
        return (int) $this->db->lastInsertId();
    }

    public function createTeenager(string $name, string $birthDate, string $email, string $password, int $parentId): int
    {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $this->db->prepare("
            INSERT INTO users (name, email, password, role, birth_date, parent_id) 
            VALUES (:name, :email, :password, 'teenager', :birth_date, :parent_id)
        ");
        
        $stmt->execute([
            'name' => $name,
            'email' => $email,
            'password' => $hashedPassword,
            'birth_date' => $birthDate,
            'parent_id' => $parentId
        ]);
        
        return (int) $this->db->lastInsertId();
    }

    public function findParentByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM users 
            WHERE email = :email AND role = 'parent' AND deleted_at IS NULL
        ");
        
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();
        
        return $user ?: null;
    }

    public function findTeenagerByName(string $name, int $parentId): ?array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM users 
            WHERE name = :name AND role = 'teenager' AND parent_id = :parent_id AND deleted_at IS NULL
        ");
        
        $stmt->execute(['name' => $name, 'parent_id' => $parentId]);
        $user = $stmt->fetch();
        
        return $user ?: null;
    }

    public function findTeenagerByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM users 
            WHERE email = :email AND role = 'teenager' AND deleted_at IS NULL
        ");
        
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();
        
        return $user ?: null;
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = :id AND deleted_at IS NULL");
        $stmt->execute(['id' => $id]);
        $user = $stmt->fetch();
        
        return $user ?: null;
    }

    public function findTeenagersByParentId(int $parentId): array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM users 
            WHERE parent_id = :parent_id AND role = 'teenager' AND deleted_at IS NULL
        ");
        
        $stmt->execute(['parent_id' => $parentId]);
        return $stmt->fetchAll();
    }

    public function updateUser(int $id, array $data): bool
    {
        $fields = [];
        $params = ['id' => $id];
        
        foreach ($data as $key => $value) {
            $fields[] = "{$key} = :{$key}";
            $params[$key] = $value;
        }
        
        $sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute($params);
    }

    public function softDelete(int $id): bool
    {
        $stmt = $this->db->prepare("UPDATE users SET deleted_at = NOW() WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}

