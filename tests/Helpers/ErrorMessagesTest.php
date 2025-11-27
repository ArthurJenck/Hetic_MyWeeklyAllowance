<?php

namespace Tests\Helpers;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestWith;
use App\Helpers\ErrorMessages;

class ErrorMessagesTest extends TestCase
{
    #[TestWith(['insufficient_funds', 'Solde insuffisant'])]
    #[TestWith(['invalid_amount', 'Montant invalide'])]
    #[TestWith(['weekly_limit_exceeded', 'Limite hebdomadaire dépassée'])]
    #[TestWith(['db', 'Une erreur est survenue. Veuillez réessayer.'])]
    #[TestWith(['email_exists', 'Cet email est déjà utilisé. Connectez-vous ou utilisez un autre email.'])]
    public function test_get_returns_correct_message_for_known_key(string $key, string $expected): void
    {
        $this->assertEquals($expected, ErrorMessages::get($key));
    }

    public function test_get_returns_default_message_for_unknown_key(): void
    {
        $this->assertEquals('Une erreur est survenue', ErrorMessages::get('unknown_key_xyz'));
    }
}
