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
}
