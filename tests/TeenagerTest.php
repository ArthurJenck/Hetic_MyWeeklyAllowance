<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use App\Teenager;
use App\Wallet;
use Exception;
use DateTime;

class TeenagerTest extends TestCase
{
    public function test_teenager_has_correct_attributes(): void
    {
        // Arrange
        $name = "Alice";
        $birthDate = "2010-05-15";

        // Act
        $teenager = new Teenager($name, $birthDate);

        // Assert
        $this->assertEquals($name, $teenager->getName());
        $this->assertEquals(new DateTime($birthDate), $teenager->getBirthDate());
    }

    public function test_teenager_must_be_older_than_10(): void
    {
        // Arrange
        // Age < 10
        $today = new DateTime();
        $birthDate = $today->modify('-9 years')->format('Y-m-d');

        // Assert & Act
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Teenager must be at least 10 years old');

        new Teenager("TooYoung", $birthDate);
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
}
