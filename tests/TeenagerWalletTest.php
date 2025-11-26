<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use App\TeenagerWallet;
use App\Transaction;
use Exception;
use DateTime;

class TeenagerWalletTest extends TestCase
{
    public function test_teenager_wallet_starts_with_zero_balance(): void
    {
        // Arrange & Act
        $wallet = new TeenagerWallet(50); // Allocation hebdo de 50

        // Assert
        $this->assertEquals(0, $wallet->getBalance());
    }

    public function test_teenager_wallet_can_receive_deposit(): void
    {
        // Arrange
        $wallet = new TeenagerWallet(50);

        // Act
        $wallet->deposit(100);

        // Assert
        $this->assertEquals(100, $wallet->getBalance());
    }

    public function test_teenager_wallet_cannot_deposit_negative_amount(): void
    {
        // Arrange
        $wallet = new TeenagerWallet(50);

        // Assert & Act
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Amount must be positive');

        $wallet->deposit(-30);
    }

    public function test_teenager_wallet_has_weekly_allowance(): void
    {
        // Arrange
        $weeklyAllowance = 75;

        // Act
        $wallet = new TeenagerWallet($weeklyAllowance);

        // Assert
        $this->assertEquals($weeklyAllowance, $wallet->getWeeklyAllowance());
    }

    public function test_teenager_wallet_tracks_weekly_remaining_balance(): void
    {
        // Arrange
        $wallet = new TeenagerWallet(100);
        $wallet->deposit(100);

        // Act
        $wallet->withdraw(30);

        // Assert
        $this->assertEquals(70, $wallet->getWeeklyRemainingBalance());
        $this->assertEquals(70, $wallet->getBalance());
    }

    public function test_teenager_wallet_resets_weekly_allowance_each_week(): void
    {
        // Arrange
        $wallet = new TeenagerWallet(50);
        $wallet->deposit(50);
        $wallet->withdraw(30);

        // Solde restant : 20, allocation hebdo restante : 20
        $this->assertEquals(20, $wallet->getWeeklyRemainingBalance());

        // Act
        $wallet->resetWeeklyAllowance();

        // Assert
        $this->assertEquals(50, $wallet->getWeeklyRemainingBalance());
        $this->assertEquals(70, $wallet->getBalance()); // 20 + 50
    }

    public function test_teenager_wallet_reset_does_not_exceed_allowance(): void
    {
        // Arrange
        $wallet = new TeenagerWallet(30);
        $wallet->deposit(30);

        // Act - Reset hebdomadaire
        $wallet->resetWeeklyAllowance();

        // Assert
        // Le solde total augmente mais l'allocation reste plafonnée
        $this->assertEquals(30, $wallet->getWeeklyRemainingBalance());
        $this->assertEquals(60, $wallet->getBalance());
    }

    public function test_teenager_wallet_can_withdraw_within_weekly_limit(): void
    {
        // Arrange
        $wallet = new TeenagerWallet(50);
        $wallet->deposit(100);

        // Act
        $wallet->withdraw(40);

        // Assert
        $this->assertEquals(60, $wallet->getBalance());
        $this->assertEquals(10, $wallet->getWeeklyRemainingBalance()); // 50 - 40
    }

    public function test_teenager_wallet_cannot_withdraw_beyond_weekly_limit(): void
    {
        // Arrange
        $wallet = new TeenagerWallet(50);
        $wallet->deposit(100);
        $wallet->withdraw(40);

        // Assert & Act
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Weekly allowance exceeded');

        $wallet->withdraw(20); // 40 + 20 = 60 > 50
    }

    public function test_teenager_wallet_cannot_withdraw_more_than_total_balance(): void
    {
        // Arrange
        $wallet = new TeenagerWallet(100);
        $wallet->deposit(30);

        // Assert & Act - Limite hebdo OK (100) mais solde total insuffisant
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Insufficient funds');

        $wallet->withdraw(50);
    }

    public function test_teenager_wallet_tracks_transaction_history(): void
    {
        // Arrange
        $wallet = new TeenagerWallet(50);

        // Act
        $wallet->deposit(100);
        $wallet->withdraw(20);
        $wallet->deposit(30);

        // Assert
        $history = $wallet->getTransactionHistory();
        $this->assertCount(3, $history);

        $this->assertInstanceOf(Transaction::class, $history[0]);
        $this->assertEquals(100, $history[0]->getAmount());
        $this->assertEquals('DEPOSIT', $history[0]->getType());

        $this->assertEquals(20, $history[1]->getAmount());
        $this->assertEquals('WITHDRAWAL', $history[1]->getType());

        $this->assertEquals(30, $history[2]->getAmount());
        $this->assertEquals('DEPOSIT', $history[2]->getType());
    }

    public function test_teenager_wallet_history_includes_all_transaction_types(): void
    {
        // Arrange
        $wallet = new TeenagerWallet(100);

        // Act
        $wallet->deposit(100);
        $wallet->withdraw(20);
        $wallet->expense(15, 'Cinéma');

        // Assert
        $history = $wallet->getTransactionHistory();
        $this->assertCount(3, $history);

        $this->assertEquals('DEPOSIT', $history[0]->getType());
        $this->assertEquals('WITHDRAWAL', $history[1]->getType());
        $this->assertEquals('EXPENSE', $history[2]->getType());
        $this->assertEquals('Cinéma', $history[2]->getDescription());
    }
}
