<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class ChatController extends Controller
{
    private $apiUrl;
    private $apiToken;

    public function __construct()
    {
        $this->apiUrl = env('LLM_API_URL');
        $this->apiToken = env('LLM_API_TOKEN');
    }

    public function chat(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'message' => 'required|string'
        ]);

        // Get conversation history from session or initialize empty array
        $messages = Session::get('chat_history', []);

        // Add user's new message to history
        $messages[] = [
            "role" => "user",
            "content" => $request->message
        ];

        // Prepare the API request
        $payload = [
            "model" => "mixtral-8x7b-Q5_K_M",
            "messages" => $messages,
            "max_tokens" => 512,
            "seed" => -1,
            "temperature" => 0,
            "top_k" => 1,
            "top_p" => 1,
            "stream" => false
        ];

        try {
            // Make the API call with bearer token
            $response = Http::withOptions([
                'verify' => false  // Disable SSL verification
            ])->withHeaders([
                'Authorization' => 'Bearer ' . $this->apiToken,
                'Content-Type' => 'application/json',
            ])->post($this->apiUrl, $payload);
            
            if ($response->successful()) {
                $result = $response->json();
                
                // Add assistant's response to history
                $assistantMessage = $result['choices'][0]['message'];
                $messages[] = $assistantMessage;
                
                // Store updated history in session
                Session::put('chat_history', $messages);

                return response()->json([
                    'success' => true,
                    'message' => $assistantMessage['content'],
                    'history' => $messages
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'API call failed',
                'error' => $response->json()
            ], $response->status());

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function clearHistory()
    {
        Session::forget('chat_history');
        return response()->json(['success' => true]);
    }
}