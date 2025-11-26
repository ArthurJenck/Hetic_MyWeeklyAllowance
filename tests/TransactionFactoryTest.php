<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestWith;
use App\TransactionFactory;
use App\Transaction;
use App\ParentWallet;
use App\TeenagerWallet;
use Exception;

class TransactionFactoryTest extends TestCase
{
    public function test_factory_creates_deposit_transaction(): void
    {
        // Arrange
        $factory = new TransactionFactory();
        $wallet = new ParentWallet();

        // Act
        $transaction = $factory->createDeposit($wallet, 100, 'Salary');

        // Assert
        $this->assertInstanceOf(Transaction::class, $transaction);
        $this->assertEquals(100, $transaction->getAmount());
        $this->assertEquals('DEPOSIT', $transaction->getType());
        $this->assertEquals('Salary', $transaction->getDescription());
    }

    public function test_factory_creates_transfer_transaction(): void
    {
        // Arrange
        $factory = new TransactionFactory();
        $parentWallet = new ParentWallet();
        $teenagerWallet = new TeenagerWallet(50);
        $parentWallet->deposit(100);

        // Act
        $transaction = $factory->createTransfer($parentWallet, $teenagerWallet, 50, 'Weekly allowance');

        // Assert
        $this->assertInstanceOf(Transaction::class, $transaction);
        $this->assertEquals(50, $transaction->getAmount());
        $this->assertEquals('TRANSFER_OUT', $transaction->getType());
        $this->assertEquals('Weekly allowance', $transaction->getDescription());
    }

    public function test_factory_creates_expense_transaction(): void
    {
        // Arrange
        $factory = new TransactionFactory();
        $wallet = new TeenagerWallet(50);
        $wallet->deposit(50);

        // Act
        $transaction = $factory->createExpense($wallet, 15, 'Movie ticket');

        // Assert
        $this->assertInstanceOf(Transaction::class, $transaction);
        $this->assertEquals(15, $transaction->getAmount());
        $this->assertEquals('EXPENSE', $transaction->getType());
        $this->assertEquals('Movie ticket', $transaction->getDescription());
    }

    #[TestWith([100.0, true])]
    #[TestWith([0.0, false])]
    #[TestWith([-50.0, false])]
    public function test_factory_validates_transaction_amount_positive(float $amount, bool $shouldSucceed): void
    {
        // Arrange
        $factory = new TransactionFactory();
        $wallet = new ParentWallet();

        if (!$shouldSucceed) {
            $this->expectException(Exception::class);
            $this->expectExceptionMessage('Amount must be positive');
        }

        // Act
        $transaction = $factory->createDeposit($wallet, $amount, 'Test');

        // Assert
        if ($shouldSucceed) {
            $this->assertInstanceOf(Transaction::class, $transaction);
        }
    }

    public function test_factory_validates_transaction_type(): void
    {
        // Arrange
        $factory = new TransactionFactory();

        // Assert & Act
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Invalid transaction type');

        $factory->validateTransactionType('INVALID_TYPE');
    }

    public function test_factory_allows_parent_transfer_if_sufficient_balance(): void
    {
        // Arrange
        $factory = new TransactionFactory();
        $parentWallet = new ParentWallet();
        $teenagerWallet = new TeenagerWallet(100);
        $parentWallet->deposit(100);

        // Act
        $transaction = $factory->createTransfer($parentWallet, $teenagerWallet, 80, 'Transfer to teenager');

        // Assert
        $this->assertInstanceOf(Transaction::class, $transaction);
        $this->assertEquals(20, $parentWallet->getBalance());
        $this->assertEquals(80, $teenagerWallet->getBalance());
    }

    public function test_factory_rejects_parent_transfer_if_insufficient_balance(): void
    {
        // Arrange
        $factory = new TransactionFactory();
        $parentWallet = new ParentWallet();
        $teenagerWallet = new TeenagerWallet(100);
        $parentWallet->deposit(50);

        // Assert & Act
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Insufficient funds');

        $factory->createTransfer($parentWallet, $teenagerWallet, 100, 'Too much');
    }

    public function test_factory_allows_teenager_withdrawal_if_weekly_limit_ok(): void
    {
        // Arrange
        $factory = new TransactionFactory();
        $wallet = new TeenagerWallet(50);
        $wallet->deposit(100);

        // Act
        $transaction = $factory->createWithdrawal($wallet, 40, 'Valid withdrawal');

        // Assert
        $this->assertInstanceOf(Transaction::class, $transaction);
        $this->assertEquals(60, $wallet->getBalance());
        $this->assertEquals(10, $wallet->getWeeklyRemainingBalance());
    }

    public function test_factory_rejects_teenager_withdrawal_if_weekly_limit_exceeded(): void
    {
        // Arrange
        $factory = new TransactionFactory();
        $wallet = new TeenagerWallet(50);
        $wallet->deposit(100);
        $wallet->withdraw(40);

        // Assert & Act
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Weekly allowance exceeded');

        $factory->createWithdrawal($wallet, 20, 'Exceeds weekly limit');
    }

    public function test_factory_rejects_teenager_withdrawal_if_total_balance_insufficient(): void
    {
        // Arrange
        $factory = new TransactionFactory();
        $wallet = new TeenagerWallet(100);
        $wallet->deposit(30);

        // Assert & Act
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Insufficient funds');

        $factory->createWithdrawal($wallet, 50, 'More than balance');
    }

    public function test_factory_allows_teenager_expense_within_limits(): void
    {
        // Arrange
        $factory = new TransactionFactory();
        $wallet = new TeenagerWallet(50);
        $wallet->deposit(50);

        // Act
        $transaction = $factory->createExpense($wallet, 25, 'Restaurant');

        // Assert
        $this->assertInstanceOf(Transaction::class, $transaction);
        $this->assertEquals(25, $wallet->getBalance());
        $this->assertEquals(25, $wallet->getWeeklyRemainingBalance());
    }

    public function test_factory_records_transaction_in_history_after_validation(): void
    {
        // Arrange
        $factory = new TransactionFactory();
        $parentWallet = new ParentWallet();
        $teenagerWallet = new TeenagerWallet(50);

        // Act
        $factory->createDeposit($parentWallet, 100, 'First deposit');
        $factory->createTransfer($parentWallet, $teenagerWallet, 30, 'Transfer to teenager');

        // Assert
        $history = $parentWallet->getTransactionHistory();
        $this->assertCount(2, $history);
        $this->assertEquals('DEPOSIT', $history[0]->getType());
        $this->assertEquals('TRANSFER_OUT', $history[1]->getType());
    }
}
