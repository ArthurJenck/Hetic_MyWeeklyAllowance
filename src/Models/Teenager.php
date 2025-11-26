<?php

namespace App\Models;

use DateTime;
use Exception;

class Teenager
{
    private ?int $id = null;
    private string $name;
    private DateTime $birthDate;
    private ?string $email = null;
    private ?string $phone = null;
    private ?string $password = null;
    private TeenagerWallet $wallet;

    public function __construct(string $name, string $birthDate, ?TeenagerWallet $wallet = null)
    {
        $this->name = $name;
        $this->birthDate = new DateTime($birthDate);

        $age = $this->getAge();
        if ($age < 10) {
            throw new Exception('Teenager must be at least 10 years old');
        }
        if ($age >= 18) {
            throw new Exception('Teenager must be under 18 years old');
        }

        $this->wallet = $wallet ?? new TeenagerWallet(0);
    }

    public function authenticate(string $username, string $password): bool
    {
        if (empty($username) || empty($password)) {
            return false;
        }

        if ($username !== $this->name) {
            return false;
        }

        if ($this->password === null) {
            return false;
        }

        return password_verify($password, $this->password);
    }

    public function setPassword(string $password): void
    {
        $this->password = password_hash($password, PASSWORD_DEFAULT);
    }

    public function getAge(): int
    {
        $now = new DateTime();
        return $now->diff($this->birthDate)->y;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function getBirthDate(): DateTime
    {
        return $this->birthDate;
    }

    public function updateEmail(string $email): void
    {
        $this->email = $email;
    }

    public function updatePhone(string $phone): void
    {
        $this->phone = $phone;
    }

    public function receiveMoney(float $amount): void
    {
        $this->wallet->deposit($amount);
    }

    public function spendMoney(float $amount, string $description): void
    {
        $this->wallet->expense($amount, $description);
    }

    public function getTotalBalance(): float
    {
        return $this->wallet->getBalance();
    }

    public function getWeeklyRemainingAllowance(): float
    {
        return $this->wallet->getWeeklyRemainingBalance();
    }

    public function getExpenseHistory(): array
    {
        $transactions = $this->wallet->getTransactionHistory();
        return array_filter($transactions, function ($transaction) {
            return $transaction->getType() === 'EXPENSE';
        });
    }

    public function getWallet(): TeenagerWallet
    {
        return $this->wallet;
    }
}
