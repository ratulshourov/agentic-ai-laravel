<?php

namespace App\AI;

class AgentTools
{
    public static function definitions(): array
    {
        return [
            self::math('add_numbers', 'Add two numbers (a + b)'),
            self::math('subtract_numbers', 'Subtract b from a (a - b)'),
            self::math('multiply_numbers', 'Multiply two numbers (a * b)'),
            self::math('divide_numbers', 'Divide a by b (a / b)'),
        ];
    }

    private static function math(string $name, string $description): array
    {
        return [
            'type' => 'function',
            'function' => [
                'name' => $name,
                'description' => $description,
                'parameters' => [
                    'type' => 'object',
                    'properties' => [
                        'a' => ['type' => 'number'],
                        'b' => ['type' => 'number'],
                    ],
                    'required' => ['a', 'b'],
                ],
            ],
        ];
    }
}
