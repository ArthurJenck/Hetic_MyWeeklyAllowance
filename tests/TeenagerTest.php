<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use App\Teenager;
use App\Wallet;
use Exception;
use DateTime;

class TeenagerTest extends TestCase
{
    public function test_teenager_can_authenticate_with_valid_credentials(): void
    {
        // Arrange
        $teenager = new Teenager("Alice", "2010-05-15");
        $teenager->setPassword("securePassword123");

        // Act
        $result = $teenager->authenticate("Alice", "securePassword123");

        // Assert
        $this->assertTrue($result);
    }

    public function test_teenager_cannot_authenticate_with_wrong_password(): void
    {
        // Arrange
        $teenager = new Teenager("Bob", "2011-03-20");
        $teenager->setPassword("correctPassword");

        // Act
        $result = $teenager->authenticate("Bob", "wrongPassword");

        // Assert
        $this->assertFalse($result);
    }

    public function test_teenager_cannot_authenticate_with_wrong_username(): void
    {
        // Arrange
        $teenager = new Teenager("Charlie", "2012-08-10");
        $teenager->setPassword("password123");

        // Act
        $result = $teenager->authenticate("WrongName", "password123");

        // Assert
        $this->assertFalse($result);
    }

    public function test_teenager_can_update_account_information(): void
    {
        // Arrange
        $teenager = new Teenager("David", "2010-01-01");

        // Act
        $teenager->updateEmail("david@example.com");
        $teenager->updatePhone("0612345678");

        // Assert
        $this->assertEquals("david@example.com", $teenager->getEmail());
        $this->assertEquals("0612345678", $teenager->getPhone());
    }

    public function test_teenager_must_be_younger_than_18(): void
    {
        // Arrange
        // Age >= 18
        $today = new DateTime();
        $birthDate = $today->modify('-19 years')->format('Y-m-d');

        // Assert & Act
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Teenager must be under 18 years old');

        new Teenager("TooOld", $birthDate);
    }

    public function test_teenager_receives_money_on_wallet(): void
    {
        // Arrange
        $walletMock = $this->createMock(Wallet::class);

        $walletMock->expects($this->once())
            ->method('deposit')
            ->with(50);

        $teenager = new Teenager("Bob", "2010-01-01", $walletMock);

        // Act
        $teenager->receiveMoney(50);
    }

    public function test_teenager_can_view_total_balance(): void
    {
        // Arrange
        $teenager = new Teenager("Emma", "2011-06-15");
        $teenager->receiveMoney(75);

        // Act
        $balance = $teenager->getTotalBalance();

        // Assert
        $this->assertEquals(75, $balance);
    }

    public function test_teenager_can_view_weekly_remaining_allowance(): void
    {
        // Arrange
        $teenager = new Teenager("Frank", "2012-02-28");
        $teenager->getWallet()->setWeeklyAllowance(50);
        $teenager->receiveMoney(50);
        $teenager->getWallet()->withdraw(20);

        // Act
        $weeklyRemaining = $teenager->getWeeklyRemainingAllowance();

        // Assert
        $this->assertEquals(30, $weeklyRemaining);
    }

    public function test_teenager_can_spend_money_within_limits(): void
    {
        // Arrange
        $teenager = new Teenager("Grace", "2010-09-05");
        $teenager->getWallet()->setWeeklyAllowance(50);
        $teenager->receiveMoney(50);

        // Act
        $teenager->spendMoney(25, "Shopping");

        // Assert
        $this->assertEquals(25, $teenager->getTotalBalance());
        $this->assertEquals(25, $teenager->getWeeklyRemainingAllowance());
    }

    public function test_teenager_cannot_spend_beyond_weekly_limit(): void
    {
        // Arrange
        $teenager = new Teenager("Henry", "2011-04-12");
        $teenager->getWallet()->setWeeklyAllowance(50);
        $teenager->receiveMoney(100);
        $teenager->spendMoney(40, "Games");

        // Assert & Act
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Weekly allowance exceeded');

        $teenager->spendMoney(20, "More games"); // 40 + 20 > 50
    }

    public function test_teenager_cannot_spend_beyond_total_balance(): void
    {
        // Arrange
        $teenager = new Teenager("Ivy", "2012-07-18");
        $teenager->getWallet()->setWeeklyAllowance(100); // Grande allocation
        $teenager->receiveMoney(30); // Mais faible solde total

        // Assert & Act
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Insufficient funds');

        $teenager->spendMoney(50, "Expensive item");
    }

    public function test_teenager_can_view_expense_history(): void
    {
        // Arrange
        $teenager = new Teenager("Jack", "2010-11-22");
        $teenager->getWallet()->setWeeklyAllowance(100);
        $teenager->receiveMoney(100);

        // Act
        $teenager->spendMoney(15, "Cinema");
        $teenager->spendMoney(10, "Snacks");
        $teenager->spendMoney(20, "Book");

        // Assert
        $history = $teenager->getExpenseHistory();
        $this->assertIsArray($history);
        $this->assertCount(3, $history);

        foreach ($history as $transaction) {
            $this->assertEquals('EXPENSE', $transaction->getType());
        }
    }
}
