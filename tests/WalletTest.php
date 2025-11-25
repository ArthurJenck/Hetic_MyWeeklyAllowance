<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use App\Wallet;
use Exception;

class WalletTest extends TestCase
{
    public function test_wallet_starts_with_zero_balance(): void
    {
        // Arrange
        $wallet = new Wallet();

        // Act
        $balance = $wallet->getBalance();

        // Assert
        $this->assertEquals(0, $balance);
    }

    public function test_it_can_deposit_money(): void
    {
        // Arrange
        $wallet = new Wallet();

        // Act
        $wallet->deposit(100);

        // Assert
        $this->assertEquals(100, $wallet->getBalance());
    }

    public function test_it_can_withdraw_money(): void
    {
        // Arrange
        $wallet = new Wallet();
        $wallet->deposit(100);

        // Act
        $wallet->withdraw(50);

        // Assert
        $this->assertEquals(50, $wallet->getBalance());
    }

    public function test_it_cannot_withdraw_more_than_balance(): void
    {
        // Arrange
        $wallet = new Wallet();
        $wallet->deposit(50);

        // Assert & Act
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Insufficient funds');

        $wallet->withdraw(100);
    }

    public function test_it_cannot_deposit_negative_amount(): void
    {
        // Arrange
        $wallet = new Wallet();

        // Assert & Act
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Amount must be positive');

        $wallet->deposit(-50);
    }

    public function test_it_cannot_withdraw_negative_amount(): void
    {
        // Arrange
        $wallet = new Wallet();

        // Assert & Act
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Amount must be positive');

        $wallet->withdraw(-20);
    }
}
