<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use App\Transaction;
use DateTime;

class TransactionTest extends TestCase
{
    public function test_transaction_stores_correct_info(): void
    {
        // Arrange
        $amount = 50.0;
        $type = 'DEPOSIT';
        $date = new DateTime();
        $description = "Allowance";

        // Act
        $transaction = new Transaction($amount, $type, $date, $description);

        // Assert
        $this->assertEquals($amount, $transaction->getAmount());
        $this->assertEquals($type, $transaction->getType());
        $this->assertEquals($date, $transaction->getDate());
        $this->assertEquals($description, $transaction->getDescription());
    }

    public function test_transaction_is_immutable(): void
    {
        // Arrange
        $transaction = new Transaction(100, 'DEPOSIT', new DateTime(), 'Test');

        // Act & Assert
        $this->assertFalse(method_exists($transaction, 'setAmount'));
        $this->assertFalse(method_exists($transaction, 'setType'));
        $this->assertFalse(method_exists($transaction, 'setDescription'));

        $this->assertEquals(100, $transaction->getAmount());
    }

    public function test_transaction_has_valid_types(): void
    {
        // Arrange & Act
        $deposit = new Transaction(50, 'DEPOSIT', new DateTime(), 'Deposit');
        $withdrawal = new Transaction(30, 'WITHDRAWAL', new DateTime(), 'Withdrawal');
        $expense = new Transaction(20, 'EXPENSE', new DateTime(), 'Expense');

        // Assert 
        $this->assertEquals('DEPOSIT', $deposit->getType());
        $this->assertEquals('WITHDRAWAL', $withdrawal->getType());
        $this->assertEquals('EXPENSE', $expense->getType());

        // Assert & Act
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Invalid transaction type');

        new Transaction(100, 'INVALID_TYPE', new DateTime(), 'Invalid');
    }

    public function test_transaction_formats_date_correctly(): void
    {
        // Arrange
        $date = new DateTime('2024-03-15 14:30:00');
        $transaction = new Transaction(75, 'DEPOSIT', $date, 'Test');

        // Act
        $formattedDate = $transaction->getFormattedDate();

        // Assert
        $this->assertEquals('2024-03-15 14:30:00', $formattedDate);
        $this->assertInstanceOf(DateTime::class, $transaction->getDate());
    }

    public function test_transaction_can_be_serialized(): void
    {
        // Arrange
        $date = new DateTime('2024-01-20 10:00:00');
        $transaction = new Transaction(125.50, 'WITHDRAWAL', $date, 'ATM');

        // Act
        $serialized = $transaction->toArray();

        // Assert
        $this->assertIsArray($serialized);
        $this->assertArrayHasKey('amount', $serialized);
        $this->assertArrayHasKey('type', $serialized);
        $this->assertArrayHasKey('date', $serialized);
        $this->assertArrayHasKey('description', $serialized);

        $this->assertEquals(125.50, $serialized['amount']);
        $this->assertEquals('WITHDRAWAL', $serialized['type']);
        $this->assertEquals('ATM', $serialized['description']);
    }
}
