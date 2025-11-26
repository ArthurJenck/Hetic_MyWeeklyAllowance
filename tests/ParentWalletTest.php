<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use App\ParentWallet;
use App\TeenagerWallet;
use App\Transaction;
use Exception;

class ParentWalletTest extends TestCase
{
    public function test_parent_wallet_starts_with_zero_balance(): void
    {
        // Arrange
        $wallet = new ParentWallet();

        // Act
        $balance = $wallet->getBalance();

        // Assert
        $this->assertEquals(0, $balance);
    }

    public function test_parent_wallet_can_deposit_money(): void
    {
        // Arrange
        $wallet = new ParentWallet();

        // Act
        $wallet->deposit(100);

        // Assert
        $this->assertEquals(100, $wallet->getBalance());
    }

    public function test_parent_wallet_cannot_deposit_negative_amount(): void
    {
        // Arrange
        $wallet = new ParentWallet();

        // Assert & Act
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Amount must be positive');

        $wallet->deposit(-50);
    }

    public function test_parent_wallet_can_transfer_to_teenager_wallet(): void
    {
        // Arrange
        $parentWallet = new ParentWallet();
        $teenagerWallet = new TeenagerWallet(50);
        $parentWallet->deposit(100);

        // Act
        $parentWallet->transferTo($teenagerWallet, 50);

        // Assert
        $this->assertEquals(50, $parentWallet->getBalance());
        $this->assertEquals(50, $teenagerWallet->getBalance());
    }

    public function test_parent_wallet_cannot_transfer_more_than_balance(): void
    {
        // Arrange
        $parentWallet = new ParentWallet();
        $teenagerWallet = new TeenagerWallet(50);
        $parentWallet->deposit(30);

        // Assert & Act
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Insufficient funds');

        $parentWallet->transferTo($teenagerWallet, 50);
    }

    public function test_parent_wallet_cannot_transfer_negative_amount(): void
    {
        // Arrange
        $parentWallet = new ParentWallet();
        $teenagerWallet = new TeenagerWallet(50);
        $parentWallet->deposit(100);

        // Assert & Act
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Amount must be positive');

        $parentWallet->transferTo($teenagerWallet, -20);
    }

    public function test_parent_wallet_receives_money_from_deleted_teenager(): void
    {
        // Arrange
        $parentWallet = new ParentWallet();
        $parentWallet->deposit(100);

        // Act
        $parentWallet->receiveFromTeenager(30);

        // Assert
        $this->assertEquals(130, $parentWallet->getBalance());
    }

    public function test_parent_wallet_tracks_transaction_history(): void
    {
        // Arrange
        $parentWallet = new ParentWallet();
        $teenagerWallet = new TeenagerWallet(50);

        // Act
        $parentWallet->deposit(100);
        $parentWallet->transferTo($teenagerWallet, 40);
        $parentWallet->receiveFromTeenager(15);

        // Assert
        $history = $parentWallet->getTransactionHistory();
        $this->assertCount(3, $history);

        $this->assertInstanceOf(Transaction::class, $history[0]);
        $this->assertEquals(100, $history[0]->getAmount());
        $this->assertEquals('DEPOSIT', $history[0]->getType());

        $this->assertEquals(40, $history[1]->getAmount());
        $this->assertEquals('TRANSFER_OUT', $history[1]->getType());

        $this->assertEquals(15, $history[2]->getAmount());
        $this->assertEquals('TRANSFER_IN', $history[2]->getType());
    }

    public function test_parent_wallet_tracks_multiple_transfers_to_teenagers(): void
    {
        // Arrange
        $parentWallet = new ParentWallet();
        $teenagerWallet1 = new TeenagerWallet(50);
        $teenagerWallet2 = new TeenagerWallet(30);
        $parentWallet->deposit(200);

        // Act
        $parentWallet->transferTo($teenagerWallet1, 50);
        $parentWallet->transferTo($teenagerWallet2, 30);
        $parentWallet->transferTo($teenagerWallet1, 20);

        // Assert
        $this->assertEquals(100, $parentWallet->getBalance());
        $history = $parentWallet->getTransactionHistory();
        $transfers = array_filter($history, fn($t) => $t->getType() === 'TRANSFER_OUT');
        $this->assertCount(3, $transfers);
    }
}
