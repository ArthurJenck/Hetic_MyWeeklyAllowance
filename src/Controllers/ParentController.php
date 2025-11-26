<?php

namespace App\Controllers;

use App\Middleware\AuthMiddleware;
use App\Repositories\UserRepository;
use App\Repositories\WalletRepository;
use App\Repositories\TransactionRepository;

class ParentController
{
    private UserRepository $userRepo;
    private WalletRepository $walletRepo;
    private TransactionRepository $transactionRepo;

    public function __construct()
    {
        $this->userRepo = new UserRepository();
        $this->walletRepo = new WalletRepository();
        $this->transactionRepo = new TransactionRepository();
    }

    public function dashboard(): void
    {
        $user = AuthMiddleware::requireParent();

        $parentData = $this->userRepo->findById($user->userId);
        $wallet = $this->walletRepo->findByUserId($user->userId);
        $teenagers = $this->userRepo->findTeenagersByParentId($user->userId);

        foreach ($teenagers as &$teenager) {
            $teenager['wallet'] = $this->walletRepo->findByUserId($teenager['id']);
        }

        require_once __DIR__ . '/../../views/parent/dashboard.php';
    }

    public function showAddTeenager(): void
    {
        AuthMiddleware::requireParent();
        require_once __DIR__ . '/../../views/parent/add-teenager.php';
    }

    public function addTeenager(): void
    {
        $user = AuthMiddleware::requireParent();

        $name = $_POST['name'] ?? '';
        $birthDate = $_POST['birth_date'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $weeklyAllowance = floatval($_POST['weekly_allowance'] ?? 0);

        try {
            $teenagerId = $this->userRepo->createTeenager($name, $birthDate, $email, $password, $user->userId);
            $this->walletRepo->create($teenagerId, $weeklyAllowance);

            header('Location: /parent/dashboard?success=teenager_added');
        } catch (\Exception $e) {
            error_log($e->getMessage());
            header('Location: /parent/add-teenager?error=db');
        }

        exit;
    }

    public function showTeenager(): void
    {
        $user = AuthMiddleware::requireParent();
        $teenagerId = $_GET['id'] ?? 0;

        $teenager = $this->userRepo->findById($teenagerId);
        $wallet = $this->walletRepo->findByUserId($teenagerId);
        $transactions = $this->transactionRepo->findByWalletId($wallet['id']);

        require_once __DIR__ . '/../../views/parent/teenager-detail.php';
    }

    public function setAllowance(): void
    {
        $user = AuthMiddleware::requireParent();

        $teenagerId = $_POST['teenager_id'] ?? 0;
        $amount = floatval($_POST['amount'] ?? 0);

        $wallet = $this->walletRepo->findByUserId($teenagerId);
        $this->walletRepo->setWeeklyAllowance($wallet['id'], $amount);

        header('Location: /parent/teenager?id=' . $teenagerId . '&success=allowance_set');
        exit;
    }

    public function transferMoney(): void
    {
        $user = AuthMiddleware::requireParent();

        $teenagerId = $_POST['teenager_id'] ?? 0;
        $amount = floatval($_POST['amount'] ?? 0);

        $parentData = $this->userRepo->findById($user->userId);
        $teenagerData = $this->userRepo->findById($teenagerId);

        $descriptionToTeenager = 'Transfert ' . \App\Helpers\TextHelper::de($parentData['name']) . $parentData['name'];
        $descriptionFromParent = 'Transfert à ' . $teenagerData['name'];

        $parentWallet = $this->walletRepo->findByUserId($user->userId);
        $teenagerWallet = $this->walletRepo->findByUserId($teenagerId);

        if ($parentWallet['balance'] < $amount) {
            header('Location: /parent/teenager?id=' . $teenagerId . '&error=insufficient_funds');
            exit;
        }

        $this->walletRepo->updateBalance($parentWallet['id'], $parentWallet['balance'] - $amount);
        $this->walletRepo->updateBalance($teenagerWallet['id'], $teenagerWallet['balance'] + $amount);

        $newWeeklyRemaining = min(
            $teenagerWallet['weekly_remaining'] + $amount,
            $teenagerWallet['weekly_allowance']
        );
        $this->walletRepo->updateWeeklyRemaining($teenagerWallet['id'], $newWeeklyRemaining);

        $this->transactionRepo->create($parentWallet['id'], $amount, 'TRANSFER_OUT', $descriptionFromParent);
        $this->transactionRepo->create($teenagerWallet['id'], $amount, 'TRANSFER_IN', $descriptionToTeenager);

        header('Location: /parent/teenager?id=' . $teenagerId . '&success=money_transferred');
        exit;
    }

    public function deleteTeenager(): void
    {
        $user = AuthMiddleware::requireParent();

        $teenagerId = $_POST['teenager_id'] ?? 0;

        $teenagerData = $this->userRepo->findById($teenagerId);
        $teenagerWallet = $this->walletRepo->findByUserId($teenagerId);
        $parentWallet = $this->walletRepo->findByUserId($user->userId);

        if ($teenagerWallet['balance'] > 0) {
            $description = 'Récupéré depuis le wallet ' . \App\Helpers\TextHelper::de($teenagerData['name']) . $teenagerData['name'];
            $this->walletRepo->updateBalance($parentWallet['id'], $parentWallet['balance'] + $teenagerWallet['balance']);
            $this->transactionRepo->create($parentWallet['id'], $teenagerWallet['balance'], 'TRANSFER_IN', $description);
        }

        $this->userRepo->softDelete($teenagerId);

        header('Location: /parent/dashboard?success=teenager_deleted');
        exit;
    }

    public function showDeposit(): void
    {
        AuthMiddleware::requireParent();
        require_once __DIR__ . '/../../views/parent/deposit.php';
    }

    public function processDeposit(): void
    {
        $user = AuthMiddleware::requireParent();

        $amount = floatval($_POST['amount'] ?? 0);

        if ($amount <= 0) {
            header('Location: /parent/deposit?error=invalid_amount');
            exit;
        }

        $parentWallet = $this->walletRepo->findByUserId($user->userId);
        $this->walletRepo->updateBalance($parentWallet['id'], $parentWallet['balance'] + $amount);
        $this->transactionRepo->create($parentWallet['id'], $amount, 'DEPOSIT', 'Dépôt sur le wallet commun');

        header('Location: /parent/dashboard?success=deposit_made');
        exit;
    }
}
