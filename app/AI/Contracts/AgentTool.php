<?php

namespace App\AI\Contracts;

interface AgentTool
{
    public function name(): string;

    public function description(): string;

    public function parameters(): array;

    public function execute(array $arguments): array;
}
