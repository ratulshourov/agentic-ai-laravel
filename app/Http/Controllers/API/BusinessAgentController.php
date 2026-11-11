<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\AI\Services\AgentService;
use Illuminate\Http\Request;

class BusinessAgentController extends Controller
{
    public function __construct(
        protected AgentService $agent
    ) {}

    public function chat(Request $request)
    {
        set_time_limit(0);

        $validated = $request->validate([
            'message' => ['required', 'string', 'min:2', 'max:2000'],
        ]);

        $answer = $this->agent->chat(
            $validated['message']
        );

        return response()->json([
            'success' => true,
            'message' => $answer,
        ]);
    }
}
