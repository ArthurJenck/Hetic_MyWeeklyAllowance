<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestWith;
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

    #[TestWith(['DEPOSIT'])]
    #[TestWith(['WITHDRAWAL'])]
    #[TestWith(['EXPENSE'])]
    #[TestWith(['TRANSFER_IN'])]
    #[TestWith(['TRANSFER_OUT'])]
    public function test_transaction_accepts_valid_types(string $type): void
    {
        // Arrange & Act
        $transaction = new Transaction(100, $type, new DateTime(), 'Test');

        // Assert
        $this->assertEquals($type, $transaction->getType());
    }

    #[TestWith(['INVALID'])]
    #[TestWith([''])]
    #[TestWith(['PAYMENT'])]
    public function test_transaction_rejects_invalid_types(string $invalidType): void
    {
        // Assert & Act
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Invalid transaction type');

        new Transaction(100, $invalidType, new DateTime(), 'Test');
    }

    #[TestWith([100.0, true])]
    #[TestWith([0.0, false])]
    #[TestWith([-50.0, false])]
    public function test_transaction_amount_validation(float $amount, bool $shouldSucceed): void
    {
        if (!$shouldSucceed) {
            $this->expectException(\Exception::class);
            $this->expectExceptionMessage('Amount must be positive');
        }

        // Arrange & Act
        $transaction = new Transaction($amount, 'DEPOSIT', new DateTime(), 'Test');

        // Assert
        if ($shouldSucceed) {
            $this->assertEquals($amount, $transaction->getAmount());
        }
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
