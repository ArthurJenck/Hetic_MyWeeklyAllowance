<?php

namespace Tests\Helpers;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestWith;
use App\Helpers\TextHelper;

class TextHelperTest extends TestCase
{
    #[TestWith(['Alice', "d'"])]
    #[TestWith(['Emma', "d'"])]
    #[TestWith(['Isabelle', "d'"])]
    #[TestWith(['Olivier', "d'"])]
    #[TestWith(['Ursula', "d'"])]
    #[TestWith(['Yann', "d'"])]
    #[TestWith(['Hélène', "d'"])]
    #[TestWith(['Émile', "d'"])]
    #[TestWith(['alice', "d'"])]
    #[TestWith(['Bob', 'de '])]
    #[TestWith(['Charlie', 'de '])]
    #[TestWith(['Jean', 'de '])]
    #[TestWith(['bob', 'de '])]
    public function test_de_returns_correct_prefix(string $name, string $expected): void
    {
        $this->assertEquals($expected, TextHelper::de($name));
    }
}
