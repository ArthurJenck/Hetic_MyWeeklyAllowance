<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestWith;
use App\Teenager;
use App\Wallet;
use Exception;
use DateTime;

class TeenagerTest extends TestCase
{
    #[TestWith(['Alice', 'correctPassword', true])]
    #[TestWith(['WrongName', 'correctPassword', false])]
    #[TestWith(['Alice', 'wrongPassword', false])]
    #[TestWith(['', 'correctPassword', false])]
    #[TestWith(['Alice', '', false])]
    public function test_teenager_authentication(string $username, string $password, bool $shouldSucceed): void
    {
        // Arrange
        $teenager = new Teenager("Alice", "2010-05-15");
        $teenager->setPassword("correctPassword");

        // Act
        $result = $teenager->authenticate($username, $password);

        // Assert
        $this->assertEquals($shouldSucceed, $result);
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

    #[TestWith([14, true])]
    #[TestWith([18, false])]
    #[TestWith([25, false])]
    public function test_teenager_age_validation(int $age, bool $shouldSucceed): void
    {
        // Arrange
        $today = new DateTime();
        $birthDate = $today->modify("-{$age} years")->format('Y-m-d');

        if (!$shouldSucceed) {
            $this->expectException(Exception::class);
            $this->expectExceptionMessage('Teenager must be under 18 years old');
        }

        // Act
        $teenager = new Teenager("Test", $birthDate);

        // Assert
        if ($shouldSucceed) {
            $this->assertInstanceOf(Teenager::class, $teenager);
        }
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
