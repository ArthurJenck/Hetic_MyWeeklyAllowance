<?php

namespace Tests\Controllers;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestWith;
use App\Controllers\ParentController;
use App\Repositories\UserRepository;
use App\Repositories\WalletRepository;
use App\Repositories\TransactionRepository;
use App\Helpers\JWTHelper;

require_once __DIR__ . '/HeaderMock.php';

class ParentControllerTest extends TestCase
{
    private $userRepo;
    private $walletRepo;
    private $transactionRepo;
    private $parentController;

    protected function setUp(): void
    {
        global $mockHeaders;
        $mockHeaders = [];

        $this->userRepo = $this->createMock(UserRepository::class);
        $this->walletRepo = $this->createMock(WalletRepository::class);
        $this->transactionRepo = $this->createMock(TransactionRepository::class);

        $this->parentController = new ParentController($this->userRepo, $this->walletRepo, $this->transactionRepo);

        $_POST = [];
        $_GET = [];
        $_COOKIE = [];

        // Arrange
        $_ENV['JWT_SECRET'] = 'test_secret_key';
        $token = JWTHelper::generateToken(1, 'parent');
        $_COOKIE['auth_token'] = $token;
    }

    public function test_dashboard_loads_data(): void
    {
        // Arrange
        $this->userRepo->method('findById')->willReturn(['id' => 1, 'name' => 'Parent']);
        $this->walletRepo->method('findByUserId')->willReturn(['id' => 1, 'balance' => 100]);
        $this->userRepo->method('findTeenagersByParentId')->willReturn([]);

        // Act
        ob_start();
        $this->parentController->dashboard();
        ob_end_clean();

        // Assert
        $this->assertTrue(true);
    }

    public function test_show_add_teenager(): void
    {
        // Act
        ob_start();
        $this->parentController->showAddTeenager();
        ob_end_clean();

        // Assert
        $this->assertTrue(true);
    }

    public function test_add_teenager_success_no_initial_amount(): void
    {
        // Arrange
        $_POST['name'] = 'Teen';
        $_POST['email'] = 'teen@test.com';
        $_POST['password'] = 'pass';
        $_POST['birth_date'] = '2010-01-01';
        $_POST['initial_amount'] = 0;

        $this->userRepo->method('createTeenager')->willReturn(2);

        // Act
        $this->parentController->addTeenager();

        // Assert
        global $mockHeaders;
        $this->assertContains('Location: /parent/dashboard?success=teenager_added', $mockHeaders);
    }

    public function test_add_teenager_success_with_initial_amount(): void
    {
        // Arrange
        $_POST['name'] = 'Teen';
        $_POST['email'] = 'teen@test.com';
        $_POST['password'] = 'pass';
        $_POST['birth_date'] = '2010-01-01';
        $_POST['initial_amount'] = 50;

        // Act
        $this->userRepo->method('createTeenager')->willReturn(2);

        $this->walletRepo->method('findByUserId')->willReturnCallback(function ($id) {
            if ($id === 1) return ['id' => 1, 'balance' => 100];
            if ($id === 2) return ['id' => 2, 'balance' => 0];
            return null;
        });

        $this->userRepo->method('findById')->with(1)->willReturn(['id' => 1, 'name' => 'Parent']);

        $this->transactionRepo->expects($this->exactly(2))->method('create');

        $this->parentController->addTeenager();

        // Assert
        global $mockHeaders;
        $this->assertContains('Location: /parent/dashboard?success=teenager_added', $mockHeaders);
    }

    public function test_add_teenager_insufficient_funds(): void
    {
        $_POST['name'] = 'Teen';
        $_POST['email'] = 'teen@test.com';
        $_POST['password'] = 'pass';
        $_POST['initial_amount'] = 500;

        $this->userRepo->method('createTeenager')->willReturn(2);
        $this->walletRepo->method('findByUserId')->willReturn(['id' => 1, 'balance' => 100]);

        // Act
        $this->parentController->addTeenager();

        global $mockHeaders;
        $this->assertContains('Location: /parent/dashboard?success=teenager_added&warning=insufficient_funds_for_initial', $mockHeaders);
    }

    #[TestWith([50, 100, true, '/parent/dashboard?success=deposit_made'])]
    #[TestWith([-10, 100, false, '/parent/deposit?error=invalid_amount'])]
    #[TestWith([0, 100, false, '/parent/deposit?error=invalid_amount'])]
    public function test_process_deposit(float $amount, float $currentBalance, bool $success, string $expectedRedirect): void
    {
        // Arrange
        $_POST['amount'] = $amount;
        $this->walletRepo->method('findByUserId')->willReturn(['id' => 1, 'balance' => $currentBalance]);

        if ($success) {
            $this->walletRepo->expects($this->once())->method('updateBalance');
            $this->transactionRepo->expects($this->once())->method('create');
        } else {
            $this->walletRepo->expects($this->never())->method('updateBalance');
        }

        // Act
        $this->parentController->processDeposit();

        // Assert
        global $mockHeaders;
        $this->assertContains('Location: ' . $expectedRedirect, $mockHeaders);
    }

    #[TestWith([10, 100, true, '/parent/teenager?id=2&success=money_transferred'])]
    #[TestWith([1000, 100, false, '/parent/teenager?id=2&error=insufficient_funds'])]
    public function test_transfer_money(float $amount, float $parentBalance, bool $success, string $expectedRedirect): void
    {
        // Arrange
        $_POST['teenager_id'] = 2;
        $_POST['amount'] = $amount;

        $this->userRepo->method('findById')->willReturnMap([
            [1, ['id' => 1, 'name' => 'Parent']],
            [2, ['id' => 2, 'name' => 'Teen']]
        ]);

        $this->walletRepo->method('findByUserId')->willReturnMap([
            [1, ['id' => 1, 'balance' => $parentBalance]],
            [2, ['id' => 2, 'balance' => 10, 'weekly_remaining' => 5, 'weekly_allowance' => 10]]
        ]);

        if ($success) {
            $this->walletRepo->expects($this->exactly(2))->method('updateBalance');
        } else {
            $this->walletRepo->expects($this->never())->method('updateBalance');
        }

        // Act
        $this->parentController->transferMoney();

        // Assert
        global $mockHeaders;
        $this->assertContains('Location: ' . $expectedRedirect, $mockHeaders);
    }

    public function test_set_allowance(): void
    {
        // Arrange
        $_POST['teenager_id'] = 2;
        $_POST['amount'] = 20;

        $this->walletRepo->method('findByUserId')->willReturn(['id' => 2]);
        $this->walletRepo->expects($this->once())->method('setWeeklyAllowance')->with(2, 20);

        // Act
        $this->parentController->setAllowance();

        // Assert
        global $mockHeaders;
        $this->assertContains('Location: /parent/teenager?id=2&success=allowance_set', $mockHeaders);
    }

    public function test_delete_teenager(): void
    {
        // Arrange
        $_POST['teenager_id'] = 2;

        $this->userRepo->method('findById')->willReturn(['id' => 2, 'name' => 'Teen']);

        $this->walletRepo->method('findByUserId')->willReturnMap([
            [2, ['id' => 2, 'balance' => 50]],
            [1, ['id' => 1, 'balance' => 100]]
        ]);

        $this->walletRepo->expects($this->once())->method('updateBalance')->with(1, 150);
        $this->userRepo->expects($this->once())->method('softDelete')->with(2);

        // Act
        $this->parentController->deleteTeenager();

        // Assert
        global $mockHeaders;
        $this->assertContains('Location: /parent/dashboard?success=teenager_deleted', $mockHeaders);
    }

    public function test_show_teenager(): void
    {
        // Arrange
        $_GET['id'] = 2;
        $this->userRepo->method('findById')->willReturn(['id' => 2, 'name' => 'Teen']);
        $this->walletRepo->method('findByUserId')->willReturn([
            'id' => 2,
            'balance' => 0,
            'weekly_allowance' => 0,
            'weekly_remaining' => 0
        ]);
        $this->transactionRepo->method('findByWalletId')->willReturn([]);

        // Act
        ob_start();
        $this->parentController->showTeenager();
        ob_end_clean();

        // Assert
        $this->assertTrue(true);
    }

    public function test_show_deposit(): void
    {
        // Act
        ob_start();
        $this->parentController->showDeposit();
        ob_end_clean();

        // Assert
        $this->assertTrue(true);
    }

    public function test_add_teenager_handles_database_error(): void
    {
        // Arrange
        $_POST['name'] = 'Teen';
        $_POST['email'] = 'teen@test.com';
        $_POST['password'] = 'pass';
        $_POST['birth_date'] = '2010-01-01';
        $_POST['initial_amount'] = 0;

        $this->userRepo->method('createTeenager')->willThrowException(new \Exception('DB Error'));

        // Act
        $this->parentController->addTeenager();

        // Assert
        global $mockHeaders;
        $this->assertContains('Location: /parent/add-teenager?error=db', $mockHeaders);
    }

    public function test_add_teenager_with_initial_amount_no_weekly_allowance(): void
    {
        // Arrange
        $_POST['name'] = 'Teen';
        $_POST['email'] = 'teen@test.com';
        $_POST['password'] = 'pass';
        $_POST['birth_date'] = '2010-01-01';
        $_POST['weekly_allowance'] = 0;
        $_POST['initial_amount'] = 50;

        $this->userRepo->method('createTeenager')->willReturn(2);

        $this->walletRepo->method('findByUserId')->willReturnCallback(function ($id) {
            if ($id === 1) return ['id' => 1, 'balance' => 100];
            if ($id === 2) return ['id' => 2, 'balance' => 0];
            return null;
        });

        $this->userRepo->method('findById')->with(1)->willReturn(['id' => 1, 'name' => 'Parent']);

        $this->transactionRepo->expects($this->exactly(2))->method('create');

        // Act
        $this->parentController->addTeenager();

        // Assert
        global $mockHeaders;
        $this->assertContains('Location: /parent/dashboard?success=teenager_added', $mockHeaders);
    }

    public function test_parent_controller_with_default_dependencies(): void
    {
        // Act
        $controller = new ParentController();

        // Assert
        $this->assertInstanceOf(ParentController::class, $controller);
    }
}
