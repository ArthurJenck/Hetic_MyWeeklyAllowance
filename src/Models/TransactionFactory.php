<?php

namespace App\Models;

use Exception;

class TransactionFactory
{
    private const VALID_TYPES = ['DEPOSIT', 'WITHDRAWAL', 'EXPENSE', 'TRANSFER_IN', 'TRANSFER_OUT'];

    public function createDeposit($wallet, float $amount, string $description): Transaction
    {
        if ($amount <= 0) {
            throw new Exception('Amount must be positive');
        }

        $wallet->deposit($amount);
        return new Transaction($amount, 'DEPOSIT', new \DateTime(), $description);
    }

    public function createTransfer(ParentWallet $parentWallet, TeenagerWallet $teenagerWallet, float $amount, string $description): Transaction
    {
        if ($amount <= 0) {
            throw new Exception('Amount must be positive');
        }

        if ($amount > $parentWallet->getBalance()) {
            throw new Exception('Insufficient funds');
        }

        $parentWallet->transferTo($teenagerWallet, $amount);
        return new Transaction($amount, 'TRANSFER_OUT', new \DateTime(), $description);
    }

    public function createWithdrawal(TeenagerWallet $wallet, float $amount, string $description): Transaction
    {
        if ($amount <= 0) {
            throw new Exception('Amount must be positive');
        }

        if ($amount > $wallet->getBalance()) {
            throw new Exception('Insufficient funds');
        }

        if ($amount > $wallet->getWeeklyRemainingBalance()) {
            throw new Exception('Weekly allowance exceeded');
        }

        $wallet->withdraw($amount);
        return new Transaction($amount, 'WITHDRAWAL', new \DateTime(), $description);
    }

    public function createExpense(TeenagerWallet $wallet, float $amount, string $description): Transaction
    {
        if ($amount <= 0) {
            throw new Exception('Amount must be positive');
        }

        if ($amount > $wallet->getBalance()) {
            throw new Exception('Insufficient funds');
        }

        if ($amount > $wallet->getWeeklyRemainingBalance()) {
            throw new Exception('Weekly allowance exceeded');
        }

        $wallet->expense($amount, $description);
        return new Transaction($amount, 'EXPENSE', new \DateTime(), $description);
    }

    public function validateTransactionType(string $type): void
    {
        if (!in_array($type, self::VALID_TYPES)) {
            throw new Exception('Invalid transaction type');
        }
    }
}
