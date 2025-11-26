<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use App\Models\ParentUser;
use App\Models\Teenager;
use App\Models\ParentWallet;
use App\Models\TeenagerWallet;
use Exception;

class ParentUserTest extends TestCase
{
    public function test_parent_can_add_teenager(): void
    {
        // Arrange
        $parent = new ParentUser("John Doe");
        $teenagerMock = $this->createMock(Teenager::class);

        // Act
        $parent->addTeenager($teenagerMock);

        // Assert
        $this->assertCount(1, $parent->getTeenagers());
        $this->assertSame($teenagerMock, $parent->getTeenagers()[0]);
    }

    public function test_parent_can_give_pocket_money(): void
    {
        // Arrange
        $parent = new ParentUser("John Doe");
        $teenagerMock = $this->createMock(Teenager::class);

        $teenagerMock->expects($this->once())
            ->method('receiveMoney')
            ->with(50);

        // Act
        $parent->giveMoney($teenagerMock, 50);
    }

    public function test_parent_can_be_created_with_valid_name(): void
    {
        // Arrange & Act
        $parent = new ParentUser("Jane Smith");

        // Assert
        $this->assertEquals("Jane Smith", $parent->getName());
        $this->assertInstanceOf(ParentWallet::class, $parent->getWallet());
    }

    public function test_parent_cannot_be_created_with_invalid_name(): void
    {
        // Assert & Act
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Name cannot be empty');

        new ParentUser("");
    }

    public function test_parent_can_update_account_information(): void
    {
        // Arrange
        $parent = new ParentUser("Old Name");

        // Act
        $parent->updateName("New Name");
        $parent->updateEmail("newemail@example.com");

        // Assert
        $this->assertEquals("New Name", $parent->getName());
        $this->assertEquals("newemail@example.com", $parent->getEmail());
    }

    public function test_parent_can_delete_account(): void
    {
        // Arrange
        $parent = new ParentUser("To Delete");

        // Act
        $result = $parent->deleteAccount();

        // Assert
        $this->assertTrue($result);
        $this->assertTrue($parent->isDeleted());
    }

    public function test_parent_cannot_add_teenager_over_18(): void
    {
        // Arrange
        $parent = new ParentUser("Parent");
        $teenagerOver18 = $this->createMock(Teenager::class);
        $teenagerOver18->method('getAge')->willReturn(19);

        // Assert & Act
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Cannot add teenager over 18 years old');

        $parent->addTeenager($teenagerOver18);
    }

    public function test_parent_can_update_teenager_information(): void
    {
        // Arrange
        $parent = new ParentUser("Parent");
        $teenager = new Teenager("Alice", "2010-05-15");
        $parent->addTeenager($teenager);

        // Act
        $parent->updateTeenagerName($teenager, "Alice Johnson");

        // Assert
        $this->assertEquals("Alice Johnson", $teenager->getName());
    }

    public function test_parent_can_delete_teenager_account(): void
    {
        // Arrange
        $parent = new ParentUser("Parent");
        $teenager = new Teenager("Bob", "2012-03-20");
        $parent->addTeenager($teenager);

        // Act
        $parent->deleteTeenager($teenager);

        // Assert
        $this->assertCount(0, $parent->getTeenagers());
    }

    public function test_parent_deleting_teenager_transfers_remaining_money_to_parent_wallet(): void
    {
        // Arrange
        $parent = new ParentUser("Parent");
        $parent->getWallet()->deposit(100);

        $teenager = new Teenager("Charlie", "2011-08-10");
        $parent->addTeenager($teenager);
        $teenager->receiveMoney(50);

        $initialParentBalance = $parent->getWallet()->getBalance();

        // Act
        $parent->deleteTeenager($teenager);

        // Assert - L'argent du teenager est transféré au parent
        $this->assertEquals($initialParentBalance + 50, $parent->getWallet()->getBalance());
    }

    public function test_parent_can_set_weekly_allowance_for_teenager(): void
    {
        // Arrange
        $parent = new ParentUser("Parent");
        $teenager = new Teenager("David", "2010-01-01");
        $parent->addTeenager($teenager);

        // Act
        $parent->setWeeklyAllowance($teenager, 75);

        // Assert
        $this->assertEquals(75, $teenager->getWallet()->getWeeklyAllowance());
    }

    public function test_parent_can_deposit_money_to_wallet(): void
    {
        // Arrange
        $parent = new ParentUser("Parent");

        // Act
        $parent->getWallet()->deposit(500);

        // Assert
        $this->assertEquals(500, $parent->getWallet()->getBalance());
    }

    public function test_parent_can_transfer_money_to_teenager(): void
    {
        // Arrange
        $parent = new ParentUser("Parent");
        $teenager = new Teenager("Alice", "2010-05-15");
        $parent->addTeenager($teenager);
        $parent->getWallet()->deposit(100);

        // Act
        $parent->getWallet()->transferTo($teenager->getWallet(), 50);

        // Assert
        $this->assertEquals(50, $parent->getWallet()->getBalance());
        $this->assertEquals(50, $teenager->getWallet()->getBalance());
    }

    public function test_parent_can_view_teenager_expense_history(): void
    {
        // Arrange
        $parent = new ParentUser("Parent");
        $teenager = new Teenager("Emma", "2011-06-15");
        $parent->addTeenager($teenager);

        $teenager->receiveMoney(50);
        $teenager->getWallet()->expense(15, 'Cinema');
        $teenager->getWallet()->expense(10, 'Snacks');

        // Act
        $history = $parent->viewTeenagerExpenseHistory($teenager);

        // Assert
        $this->assertIsArray($history);
        $this->assertGreaterThanOrEqual(2, count($history));

        $expenseTransactions = array_filter($history, function ($transaction) {
            return $transaction->getType() === 'EXPENSE';
        });
        $this->assertCount(2, $expenseTransactions);
    }
}
