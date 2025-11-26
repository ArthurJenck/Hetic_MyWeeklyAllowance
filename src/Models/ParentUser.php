<?php

namespace App\Models;

use Exception;

class ParentUser
{
    private ?int $id = null;
    private string $name;
    private ?string $email = null;
    private ?string $password = null;
    private ParentWallet $wallet;
    private array $teenagers = [];
    private bool $deleted = false;

    public function __construct(string $name)
    {
        if (empty($name)) {
            throw new Exception('Name cannot be empty');
        }

        $this->name = $name;
        $this->wallet = new ParentWallet();
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getWallet(): ParentWallet
    {
        return $this->wallet;
    }

    public function getTeenagers(): array
    {
        return $this->teenagers;
    }

    public function updateName(string $name): void
    {
        if (empty($name)) {
            throw new Exception('Name cannot be empty');
        }
        $this->name = $name;
    }

    public function updateEmail(string $email): void
    {
        $this->email = $email;
    }

    public function setPassword(string $password): void
    {
        $this->password = password_hash($password, PASSWORD_DEFAULT);
    }

    public function deleteAccount(): bool
    {
        $this->deleted = true;
        return true;
    }

    public function isDeleted(): bool
    {
        return $this->deleted;
    }

    public function addTeenager(Teenager $teenager): void
    {
        if ($teenager->getAge() >= 18) {
            throw new Exception('Cannot add teenager over 18 years old');
        }
        $this->teenagers[] = $teenager;
    }

    public function giveMoney(Teenager $teenager, float $amount): void
    {
        $teenager->receiveMoney($amount);
    }

    public function updateTeenagerName(Teenager $teenager, string $name): void
    {
        $reflection = new \ReflectionClass($teenager);
        $property = $reflection->getProperty('name');
        $property->setAccessible(true);
        $property->setValue($teenager, $name);
    }

    public function deleteTeenager(Teenager $teenager): void
    {
        $teenagerBalance = $teenager->getTotalBalance();
        if ($teenagerBalance > 0) {
            $this->wallet->receiveFromTeenager($teenagerBalance);
        }

        $key = array_search($teenager, $this->teenagers, true);
        if ($key !== false) {
            unset($this->teenagers[$key]);
            $this->teenagers = array_values($this->teenagers);
        }
    }

    public function setWeeklyAllowance(Teenager $teenager, float $amount): void
    {
        $teenager->getWallet()->setWeeklyAllowance($amount);
    }

    public function viewTeenagerExpenseHistory(Teenager $teenager): array
    {
        return $teenager->getExpenseHistory();
    }
}

