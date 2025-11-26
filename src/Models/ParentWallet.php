<?php

namespace App\Models;

use Exception;

class ParentWallet
{
    private float $balance = 0;
    private array $transactions = [];

    public function getBalance(): float
    {
        return $this->balance;
    }

    public function deposit(float $amount): void
    {
        if ($amount <= 0) {
            throw new Exception('Amount must be positive');
        }

        $this->balance += $amount;
        $this->transactions[] = new Transaction($amount, 'DEPOSIT', new \DateTime(), 'Deposit to parent wallet');
    }

    public function transferTo(TeenagerWallet $teenagerWallet, float $amount): void
    {
        if ($amount <= 0) {
            throw new Exception('Amount must be positive');
        }

        if ($amount > $this->balance) {
            throw new Exception('Insufficient funds');
        }

        $this->balance -= $amount;
        $this->transactions[] = new Transaction($amount, 'TRANSFER_OUT', new \DateTime(), 'Transfer to teenager');

        $teenagerWallet->deposit($amount);
    }

    public function receiveFromTeenager(float $amount): void
    {
        $this->balance += $amount;
        $this->transactions[] = new Transaction($amount, 'TRANSFER_IN', new \DateTime(), 'Received from teenager');
    }

    public function getTransactionHistory(): array
    {
        return $this->transactions;
    }
}
