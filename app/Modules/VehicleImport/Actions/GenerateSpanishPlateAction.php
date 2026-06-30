<?php

namespace App\Modules\VehicleImport\Actions;

class GenerateSpanishPlateAction
{
    public function execute(): string
    {
        $numbers = sprintf('%04d', random_int(0, 9999));
        $letters = $this->generateLetters();
        
        return "{$numbers}-{$letters}";
    }

    private function generateLetters(): string
    {
        $vowels = ['A', 'E', 'I', 'O', 'U'];
        $consonants = ['B', 'C', 'D', 'F', 'G', 'H', 'J', 'K', 'L', 'M', 'N', 'P', 'Q', 'R', 'S', 'T', 'V', 'W', 'X', 'Y', 'Z'];
        
        $letters = '';
        for ($i = 0; $i < 3; $i++) {
            $pool = $i === 0 || $i === 2 ? $consonants : $vowels;
            $letters .= $pool[array_rand($pool)];
        }
        
        return $letters;
    }
}