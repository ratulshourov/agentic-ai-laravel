<?php

namespace App\AI;

use App\Tools\CalculatorTool;
use InvalidArgumentException;

class ToolExecutor
{
    public function execute(string $name, array $arguments): mixed
    {
        return match ($name) {
            'add_numbers' => $this->calculate('add', $arguments),
            'subtract_numbers' => $this->calculate('subtract', $arguments),
            'multiply_numbers' => $this->calculate('multiply', $arguments),
            'divide_numbers' => $this->calculate('divide', $arguments),
            default => throw new InvalidArgumentException(
                "Unknown tool: {$name}"
            ),
        };
    }

    private function calculate(string $operation, array $arguments): float
    {
        if (
            !isset($arguments['a'], $arguments['b']) ||
            !is_numeric($arguments['a']) ||
            !is_numeric($arguments['b'])
        ) {
            throw new InvalidArgumentException(
                'Valid numbers a and b are required.'
            );
        }

        return app(CalculatorTool::class)->{$operation}(
            (float) $arguments['a'],
            (float) $arguments['b']
        );
    }
}
