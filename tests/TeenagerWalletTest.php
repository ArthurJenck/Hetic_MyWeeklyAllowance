<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestWith;
use App\Models\TeenagerWallet;
use App\Models\Transaction;
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

    #[TestWith([50.0, true])]
    #[TestWith([0.0, false])]
    #[TestWith([-10.0, false])]
    public function test_teenager_wallet_deposit_amount_validation(float $amount, bool $shouldSucceed): void
    {
        // Arrange
        $wallet = new TeenagerWallet(50);

        if (!$shouldSucceed) {
            $this->expectException(Exception::class);
            $this->expectExceptionMessage('Amount must be positive');
        }

        // Act
        $wallet->deposit($amount);

        // Assert
        if ($shouldSucceed) {
            $this->assertEquals($amount, $wallet->getBalance());
        }
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

    #[TestWith([20.0, true])]
    #[TestWith([0.0, false])]
    #[TestWith([-10.0, false])]
    public function test_teenager_wallet_withdraw_amount_validation(float $amount, bool $shouldSucceed): void
    {
        // Arrange
        $wallet = new TeenagerWallet(50);
        $wallet->deposit(100);

        if (!$shouldSucceed) {
            $this->expectException(Exception::class);
            $this->expectExceptionMessage('Amount must be positive');
        }

        // Act
        $wallet->withdraw($amount);

        // Assert
        if ($shouldSucceed) {
            $this->assertEquals(100 - $amount, $wallet->getBalance());
        }
    }

    #[TestWith([30.0, true])]
    #[TestWith([50.0, true])]
    #[TestWith([60.0, false])]
    public function test_teenager_wallet_withdraw_weekly_limit_validation(float $withdrawAmount, bool $shouldSucceed): void
    {
        // Arrange
        $wallet = new TeenagerWallet(50);
        $wallet->deposit(100);

        if (!$shouldSucceed) {
            $this->expectException(Exception::class);
            $this->expectExceptionMessage('Weekly allowance exceeded');
        }

        // Act
        $wallet->withdraw($withdrawAmount);

        // Assert
        if ($shouldSucceed) {
            $this->assertEquals(100 - $withdrawAmount, $wallet->getBalance());
        }
    }

    #[TestWith([20.0, true])]
    #[TestWith([30.0, true])]
    #[TestWith([50.0, false])]
    public function test_teenager_wallet_withdraw_balance_validation(float $withdrawAmount, bool $shouldSucceed): void
    {
        // Arrange
        $wallet = new TeenagerWallet(100);
        $wallet->deposit(30);

        if (!$shouldSucceed) {
            $this->expectException(Exception::class);
            $this->expectExceptionMessage('Insufficient funds');
        }

        // Act
        $wallet->withdraw($withdrawAmount);

        // Assert
        if ($shouldSucceed) {
            $this->assertEquals(30 - $withdrawAmount, $wallet->getBalance());
        }
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
