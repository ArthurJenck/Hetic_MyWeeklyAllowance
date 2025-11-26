<?php

namespace App\Models;

use Exception;

class TeenagerWallet
{
    private float $balance = 0;
    private float $weeklyAllowance;
    private float $weeklyRemainingBalance;
    private array $transactions = [];

    public function __construct(float $weeklyAllowance)
    {
        $this->weeklyAllowance = $weeklyAllowance;
        $this->weeklyRemainingBalance = 0;
    }

    public function getBalance(): float
    {
        return $this->balance;
    }

    public function getWeeklyAllowance(): float
    {
        return $this->weeklyAllowance;
    }

    public function setWeeklyAllowance(float $amount): void
    {
        $this->weeklyAllowance = $amount;
    }

    public function getWeeklyRemainingBalance(): float
    {
        return $this->weeklyRemainingBalance;
    }

    public function deposit(float $amount): void
    {
        if ($amount <= 0) {
            throw new Exception('Amount must be positive');
        }

        $this->balance += $amount;

        if ($this->weeklyAllowance > 0) {
            $this->weeklyRemainingBalance = min($this->balance, $this->weeklyAllowance);
        } else {
            $this->weeklyRemainingBalance = $this->balance;
        }

        $this->transactions[] = new Transaction($amount, 'DEPOSIT', new \DateTime(), 'Deposit');
    }

    public function withdraw(float $amount): void
    {
        if ($amount <= 0) {
            throw new Exception('Amount must be positive');
        }

        if ($amount > $this->balance) {
            throw new Exception('Insufficient funds');
        }

        if ($this->weeklyAllowance > 0 && $amount > $this->weeklyRemainingBalance) {
            throw new Exception('Weekly allowance exceeded');
        }

        $this->balance -= $amount;
        if ($this->weeklyAllowance > 0) {
            $this->weeklyRemainingBalance -= $amount;
        }
        $this->transactions[] = new Transaction($amount, 'WITHDRAWAL', new \DateTime(), 'Withdrawal');
    }

    public function expense(float $amount, string $description): void
    {
        if ($amount <= 0) {
            throw new Exception('Amount must be positive');
        }

        if ($amount > $this->balance) {
            throw new Exception('Insufficient funds');
        }

        if ($this->weeklyAllowance > 0 && $amount > $this->weeklyRemainingBalance) {
            throw new Exception('Weekly allowance exceeded');
        }

        $this->balance -= $amount;
        if ($this->weeklyAllowance > 0) {
            $this->weeklyRemainingBalance -= $amount;
        }
        $this->transactions[] = new Transaction($amount, 'EXPENSE', new \DateTime(), $description);
    }

    public function resetWeeklyAllowance(): void
    {
        // Le reset réinitialise uniquement le compteur hebdomadaire, plafonné au balance réel
        $this->weeklyRemainingBalance = min($this->weeklyAllowance, $this->balance);
    }

    public function getTransactionHistory(): array
    {
        return $this->transactions;
    }
}
