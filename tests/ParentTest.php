<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use App\ParentUser;
use App\Teenager;

class ParentTest extends TestCase
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
}
