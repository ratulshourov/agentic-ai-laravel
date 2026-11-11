<?php

namespace App\AI\Tools;

use App\AI\Contracts\AgentTool;
use App\Models\Product;
use Illuminate\Support\Facades\Validator;

class StockTool implements AgentTool
{
    public function name(): string
    {
        return 'check_stock';
    }

    public function description(): string
    {
        return 'Check available stock for a product by product ID.';
    }

    public function parameters(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'product_id' => [
                    'type' => 'integer',
                    'description' => 'Product ID',
                ],
            ],
            'required' => ['product_id'],
        ];
    }

    public function execute(array $arguments): array
    {
        $validator = Validator::make($arguments, [
            'product_id' => ['required', 'integer', 'min:1'],
        ]);

        if ($validator->fails()) {
            return [
                'success' => false,
                'message' => 'Invalid product ID.',
            ];
        }

        //$merchantId = auth()->user()?->merchant?->id;

        // if (!$merchantId) {
        //     return [
        //         'success' => false,
        //         'message' => 'Merchant account not found.',
        //     ];
        // }

        $product = Product::query()
            ->where('id', $arguments['product_id'])
            ->first(['id', 'name', 'total_stock_qty']);

        if (!$product) {
            return [
                'success' => false,
                'message' => 'Product not found.',
            ];
        }

        return [
            'success' => true,
            'data' => [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'available_stock' => $product->total_stock_qty,
            ],
        ];
    }
}
