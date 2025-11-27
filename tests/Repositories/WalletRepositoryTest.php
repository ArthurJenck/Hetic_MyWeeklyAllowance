<?php

namespace Tests\Repositories;

use App\Repositories\WalletRepository;

class WalletRepositoryTest extends RepositoryTestCase
{
    private WalletRepository $walletRepo;
    private int $userId;

    protected function setUp(): void
    {
        parent::setUp();
        $this->walletRepo = new WalletRepository($this->pdo);

        // Arrange
        $this->pdo->exec("INSERT INTO users (name, email, password, role) VALUES ('User', 'u@test.com', 'pass', 'parent')");
        $this->userId = (int) $this->pdo->lastInsertId();
    }

    public function test_create_creates_wallet(): void
    {
        // Act
        $walletId = $this->walletRepo->create($this->userId, 50.00);

        // Assert
        $this->assertGreaterThan(0, $walletId);

        $wallet = $this->walletRepo->findByUserId($this->userId);
        $this->assertEquals(50.00, $wallet['weekly_allowance']);
        $this->assertEquals(0.00, $wallet['balance']);
    }

    public function test_update_balance(): void
    {
        // Arrange
        $walletId = $this->walletRepo->create($this->userId);

        // Act
        $this->walletRepo->updateBalance($walletId, 100.50);

        // Assert
        $wallet = $this->walletRepo->findByUserId($this->userId);
        $this->assertEquals(100.50, $wallet['balance']);
    }

    public function test_update_weekly_remaining(): void
    {
        // Arrange
        $walletId = $this->walletRepo->create($this->userId, 50.00);

        // Act
        $this->walletRepo->updateWeeklyRemaining($walletId, 25.00);

        // Assert
        $wallet = $this->walletRepo->findByUserId($this->userId);
        $this->assertEquals(25.00, $wallet['weekly_remaining']);
    }

    public function test_set_weekly_allowance(): void
    {
        // Arrange
        $walletId = $this->walletRepo->create($this->userId, 50.00);

        // Act
        $this->walletRepo->setWeeklyAllowance($walletId, 100.00);

        // Assert
        $wallet = $this->walletRepo->findByUserId($this->userId);
        $this->assertEquals(100.00, $wallet['weekly_allowance']);
        $this->assertEquals(100.00, $wallet['weekly_remaining']);
    }

    public function test_reset_weekly_allowance(): void
    {
        // Arrange
        $walletId = $this->walletRepo->create($this->userId, 20.00);
        $this->walletRepo->updateBalance($walletId, 10.00);

        // Act
        $this->walletRepo->resetWeeklyAllowance($walletId);

        // Assert
        $wallet = $this->walletRepo->findByUserId($this->userId);

        $this->assertEquals(30.00, $wallet['balance']);
        $this->assertEquals(20.00, $wallet['weekly_remaining']);
        $this->assertEquals(date('Y-m-d'), $wallet['last_reset_date']);
    }
}
