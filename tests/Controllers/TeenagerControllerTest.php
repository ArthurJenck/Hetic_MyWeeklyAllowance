<?php

namespace Tests\Controllers;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestWith;
use App\Controllers\TeenagerController;
use App\Repositories\WalletRepository;
use App\Repositories\TransactionRepository;
use App\Helpers\JWTHelper;

require_once __DIR__ . '/HeaderMock.php';

class TeenagerControllerTest extends TestCase
{
    private $walletRepo;
    private $transactionRepo;
    private $teenagerController;

    protected function setUp(): void
    {
        global $mockHeaders;
        $mockHeaders = [];

        $this->walletRepo = $this->createMock(WalletRepository::class);
        $this->transactionRepo = $this->createMock(TransactionRepository::class);

        $this->teenagerController = new TeenagerController($this->walletRepo, $this->transactionRepo);

        $_POST = [];
        $_COOKIE = [];

        // Arrange
        $_ENV['JWT_SECRET'] = 'test_secret_key';
        $token = JWTHelper::generateToken(2, 'teenager');
        $_COOKIE['auth_token'] = $token;
    }

    public function test_dashboard(): void
    {
        // Arrange
        $this->walletRepo->method('findByUserId')->willReturn([
            'id' => 2,
            'balance' => 0,
            'weekly_allowance' => 0,
            'weekly_remaining' => 0
        ]);
        $this->transactionRepo->method('findByWalletId')->willReturn([]);

        // Act
        ob_start();
        $this->teenagerController->dashboard();
        ob_end_clean();

        // Assert
        $this->assertTrue(true);
    }

    public function test_history(): void
    {
        // Arrange
        $this->walletRepo->method('findByUserId')->willReturn(['id' => 2]);
        $this->transactionRepo->method('findByWalletId')->willReturn([]);

        // Act
        ob_start();
        $this->teenagerController->history();
        ob_end_clean();

        // Assert
        $this->assertTrue(true);
    }

    #[TestWith([10, 50, 20, 20, true, '/teenager/dashboard?success=expense_recorded'])]
    #[TestWith([100, 50, 20, 20, false, '/teenager/dashboard?error=insufficient_funds'])]
    #[TestWith([-5, 50, 20, 20, false, '/teenager/dashboard?error=invalid_amount'])]
    #[TestWith([15, 100, 20, 10, false, '/teenager/dashboard?error=weekly_limit_exceeded'])]
    public function test_expense(float $amount, float $balance, float $weeklyAllowance, float $weeklyRemaining, bool $success, string $expectedRedirect): void
    {
        // Arrange
        $_POST['amount'] = $amount;
        $_POST['description'] = 'Snacks';

        $this->walletRepo->method('findByUserId')->willReturn([
            'id' => 2,
            'balance' => $balance,
            'weekly_allowance' => $weeklyAllowance,
            'weekly_remaining' => $weeklyRemaining
        ]);

        if ($success) {
            $this->walletRepo->expects($this->once())->method('updateBalance');
            $this->transactionRepo->expects($this->once())->method('create');
        } else {
            $this->walletRepo->expects($this->never())->method('updateBalance');
        }

        // Act
        $this->teenagerController->expense();

        // Assert
        global $mockHeaders;
        $this->assertContains('Location: ' . $expectedRedirect, $mockHeaders);
    }

    public function test_teenager_controller_with_default_dependencies(): void
    {
        // Act
        $controller = new TeenagerController();

        // Assert
        $this->assertInstanceOf(TeenagerController::class, $controller);
    }
}
