<?php

namespace App\Helpers;

class ErrorMessages
{
    public static function get(string $errorCode): string
    {
        $messages = [
            'insufficient_funds' => 'Solde insuffisant',
            'invalid_amount' => 'Montant invalide',
            'weekly_limit_exceeded' => 'Limite hebdomadaire dépassée',
            'db' => 'Une erreur est survenue. Veuillez réessayer.',
            'email_exists' => 'Cet email est déjà utilisé. Connectez-vous ou utilisez un autre email.',
        ];
        return $messages[$errorCode] ?? 'Une erreur est survenue';
    }
}
