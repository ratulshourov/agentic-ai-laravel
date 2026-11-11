<?php

namespace App\AI\Tools;

use App\AI\Contracts\AgentTool;
use App\Models\Product;
use Illuminate\Support\Facades\Validator;

class ProductTool implements AgentTool
{
    public function name(): string
    {
        return 'search_products';
    }

    public function description(): string
    {
        return 'Search products by product name.';
    }

    public function parameters(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'keyword' => [
                    'type' => 'string',
                    'description' => 'Product name to search',
                ],
            ],
            'required' => ['keyword'],
        ];
    }

    public function execute(array $arguments): array
    {
        $validator = Validator::make($arguments, [
            'keyword' => ['required', 'string', 'min:2', 'max:100'],
        ]);

        if ($validator->fails()) {
            return [
                'success' => false,
                'message' => 'Invalid keyword.',
            ];
        }

        $products = Product::query()
            ->where('name', 'like', '%' . $arguments['keyword'] . '%')
            ->limit(10)
            ->get(['id', 'name',]);

        return [
            'success' => true,
            'data' => $products->toArray(),
        ];
    }
}
