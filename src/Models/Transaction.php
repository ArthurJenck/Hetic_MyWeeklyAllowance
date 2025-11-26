<?php

namespace App\Models;

use DateTime;
use Exception;

class Transaction
{
    private const VALID_TYPES = ['DEPOSIT', 'WITHDRAWAL', 'EXPENSE', 'TRANSFER_IN', 'TRANSFER_OUT'];

    private float $amount;
    private string $type;
    private DateTime $date;
    private string $description;

    public function __construct(float $amount, string $type, DateTime $date, string $description)
    {
        if ($amount <= 0) {
            throw new Exception('Amount must be positive');
        }

        if (!in_array($type, self::VALID_TYPES)) {
            throw new Exception('Invalid transaction type');
        }

        $this->amount = $amount;
        $this->type = $type;
        $this->date = $date;
        $this->description = $description;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getDate(): DateTime
    {
        return $this->date;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getFormattedDate(): string
    {
        return $this->date->format('Y-m-d H:i:s');
    }

    public function toArray(): array
    {
        return [
            'amount' => $this->amount,
            'type' => $this->type,
            'date' => $this->getFormattedDate(),
            'description' => $this->description
        ];
    }
}
