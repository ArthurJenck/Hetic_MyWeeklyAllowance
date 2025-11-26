<?php

namespace App\Helpers;

class TextHelper
{
    public static function de(string $name): string
    {
        $vowels = ['a', 'e', 'i', 'o', 'u', 'h', 'y', 'é', 'è', 'ê', 'à', 'â'];
        $firstChar = mb_strtolower(mb_substr($name, 0, 1));
        return in_array($firstChar, $vowels) ? "d'" : 'de ';
    }
}
