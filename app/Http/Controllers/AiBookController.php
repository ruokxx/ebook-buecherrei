<?php

namespace App\Http\Controllers;

use App\Models\Ebook;
use App\Services\AiGenerationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AiBookController extends Controller
{
    protected $aiService;

    public function __construct(AiGenerationService $aiService)
    {
        $this->aiService = $aiService;
    }

    /**
     * Schow the chat interface.
     */
    public function create()
    {
        // Reset the chat history when opening the page anew
        session()->forget('ai_chat_history');

        return view('generate.chat');
    }

    /**
     * Handle the AJAX chat requests.
     */
    public function chat(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000'
        ]);

        $userMessage = $request->message;
        $history = session()->get('ai_chat_history', []);

        try {
            // Get the AI's response
            $aiResponse = $this->aiService->chatStep($history, $userMessage);

            // Update history
            $history[] = ['role' => 'user', 'content' => $userMessage];

            $isReady = false;
            if (str_contains($aiResponse, '[READY_TO_GENERATE]')) {
                $isReady = true;
                $aiResponse = str_replace('[READY_TO_GENERATE]', '', $aiResponse);
            }

            $history[] = ['role' => 'assistant', 'content' => $aiResponse];
            session()->put('ai_chat_history', $history);

            return response()->json([
                'success' => true,
                'message' => trim($aiResponse),
                'ready' => $isReady
            ]);

        }
        catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Performs the final generation and saves the book.
     */
    public function generate(Request $request)
    {
        $history = session()->get('ai_chat_history', []);

        if (empty($history)) {
            return response()->json([
                'success' => false,
                'message' => 'Kein Chat-Verlauf gefunden.'
            ], 400);
        }

        try {
            // 1. Generate the Book Content
            $bookContent = $this->aiService->generateBook($history);

            // 2. Ask the AI again just to extract a title from the context
            $title = $this->aiService->chatStep($history, 'Bitte antworte ab jetzt NUR NOCH mit dem Titel des Buches, auf den wir uns geeinigt haben. Keine weiteren Worte, keine Anführungszeichen.');
            if (empty(trim($title)) || strlen($title) > 255) {
                $title = "Ein KI-generiertes Abenteuer";
            }
            $title = trim($title);

            // 3. Save as .txt File
            $filename = Str::slug($title) . '-' . time() . '.txt';
            Storage::disk('public')->put('ebooks/' . $filename, trim($bookContent));

            // 4. Create Database Record
            $ebook = Ebook::create([
                'title' => $title,
                'filename' => $filename,
                'file_type' => 'txt',
                'chapters_count' => substr_count(strtoupper($bookContent), 'KAPITEL'),
                'genre' => 'Neuerscheinung (KI)',
                'generated_by' => auth()->id(),
            ]);

            // Clear session history
            session()->forget('ai_chat_history');

            return response()->json([
                'success' => true,
                'redirect' => route('ebooks.read', $ebook->id)
            ]);

        }
        catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
