<?php

namespace App\Controllers;

use App\Middleware\AuthMiddleware;
use App\Repositories\WalletRepository;
use App\Repositories\TransactionRepository;

class TeenagerController
{
    private WalletRepository $walletRepo;
    private TransactionRepository $transactionRepo;

    public function __construct(
        ?WalletRepository $walletRepo = null,
        ?TransactionRepository $transactionRepo = null
    ) {
        $this->walletRepo = $walletRepo ?? new WalletRepository();
        $this->transactionRepo = $transactionRepo ?? new TransactionRepository();
    }

    public function dashboard(): void
    {
        $user = AuthMiddleware::requireTeenager();

        $wallet = $this->walletRepo->findByUserId($user->userId);
        $transactions = $this->transactionRepo->findByWalletId($wallet['id']);

        require_once __DIR__ . '/../../views/teenager/dashboard.php';
    }

    public function expense(): void
    {
        $user = AuthMiddleware::requireTeenager();

        $amount = floatval($_POST['amount'] ?? 0);
        $description = $_POST['description'] ?? 'Expense';

        $wallet = $this->walletRepo->findByUserId($user->userId);

        if ($amount <= 0) {
            header('Location: /teenager/dashboard?error=invalid_amount');
            return;
        }

        if ($amount > $wallet['balance']) {
            header('Location: /teenager/dashboard?error=insufficient_funds');
            return;
        }

        if ($wallet['weekly_allowance'] > 0 && $amount > $wallet['weekly_remaining']) {
            header('Location: /teenager/dashboard?error=weekly_limit_exceeded');
            return;
        }

        $this->walletRepo->updateBalance($wallet['id'], $wallet['balance'] - $amount);

        if ($wallet['weekly_allowance'] > 0) {
            $this->walletRepo->updateWeeklyRemaining($wallet['id'], $wallet['weekly_remaining'] - $amount);
        }

        $this->transactionRepo->create($wallet['id'], $amount, 'EXPENSE', $description);

        header('Location: /teenager/dashboard?success=expense_recorded');
        return;
    }

    public function history(): void
    {
        $user = AuthMiddleware::requireTeenager();

        $wallet = $this->walletRepo->findByUserId($user->userId);
        $transactions = $this->transactionRepo->findByWalletId($wallet['id']);

        require_once __DIR__ . '/../../views/teenager/history.php';
    }
}
