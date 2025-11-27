<?php

namespace Tests\Repositories;

use App\Repositories\TransactionRepository;

class TransactionRepositoryTest extends RepositoryTestCase
{
    private TransactionRepository $transactionRepo;
    private int $walletId;

    protected function setUp(): void
    {
        parent::setUp();
        $this->transactionRepo = new TransactionRepository($this->pdo);

        // Arrange
        $this->pdo->exec("INSERT INTO users (name, email, password, role) VALUES ('User', 'u@test.com', 'pass', 'parent')");
        $userId = $this->pdo->lastInsertId();
        $this->pdo->exec("INSERT INTO wallets (user_id) VALUES ($userId)");
        $this->walletId = (int) $this->pdo->lastInsertId();
    }

    public function test_create_adds_transaction(): void
    {
        // Act
        $id = $this->transactionRepo->create($this->walletId, 50.00, 'DEPOSIT', 'Test Deposit');

        // Assert
        $this->assertGreaterThan(0, $id);

        $stmt = $this->pdo->prepare("SELECT * FROM transactions WHERE id = ?");
        $stmt->execute([$id]);
        $trx = $stmt->fetch(\PDO::FETCH_ASSOC);

        $this->assertEquals(50.00, $trx['amount']);
        $this->assertEquals('DEPOSIT', $trx['type']);
        $this->assertEquals('Test Deposit', $trx['description']);
    }

    public function test_find_by_wallet_id(): void
    {
        // Arrange
        $this->transactionRepo->create($this->walletId, 10, 'DEPOSIT', '1');
        $this->transactionRepo->create($this->walletId, 20, 'EXPENSE', '2');

        // Act
        $transactions = $this->transactionRepo->findByWalletId($this->walletId);

        // Assert
        $this->assertCount(2, $transactions);
    }

    public function test_find_expenses_by_wallet_id(): void
    {
        // Arrange
        $this->transactionRepo->create($this->walletId, 100, 'DEPOSIT', 'Init');
        $this->transactionRepo->create($this->walletId, 10, 'EXPENSE', 'Cinema');
        $this->transactionRepo->create($this->walletId, 5, 'EXPENSE', 'Candy');

        // Act
        $expenses = $this->transactionRepo->findExpensesByWalletId($this->walletId);

        // Assert
        $this->assertCount(2, $expenses);
        foreach ($expenses as $expense) {
            $this->assertEquals('EXPENSE', $expense['type']);
        }
    }
}
