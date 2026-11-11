<?php

namespace App\AI\Services;

use App\AI\Contracts\AgentTool;
use InvalidArgumentException;

class ToolRegistry
{
    protected array $tools = [];

    public function register(AgentTool $tool): void
    {
        $this->tools[$tool->name()] = $tool;
    }

    public function definitions(): array
    {
        $definitions = [];

        foreach ($this->tools as $tool) {
            $definitions[] = [
                'type' => 'function',
                'function' => [
                    'name' => $tool->name(),
                    'description' => $tool->description(),
                    'parameters' => $tool->parameters(),
                ],
            ];
        }

        return $definitions;
    }

    public function execute(
        string $name,
        array $arguments
    ): array {
        if (!isset($this->tools[$name])) {
            throw new InvalidArgumentException(
                "Tool not registered: {$name}"
            );
        }

        return $this->tools[$name]->execute($arguments);
    }
}
