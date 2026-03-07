<?php

namespace App\Services;

use App\Models\Setting;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class AiGenerationService
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => 'https://generativelanguage.googleapis.com/v1beta/models/',
            'timeout' => 120, // Book generation can take time
        ]);
    }

    /**
     * Helper to get the API Key.
     */
    protected function getApiKey()
    {
        return Setting::get('llm_api_key', config('services.gemini.key', env('GEMINI_API_KEY')));
    }

    /**
     * Maps the internal history (OpenAI style) format to Gemini's format.
     */
    protected function mapHistoryToGemini(array $history)
    {
        $geminiContents = [];
        foreach ($history as $msg) {
            if ($msg['role'] === 'system') {
                continue;
            }
            $role = $msg['role'] === 'assistant' ? 'model' : 'user';
            $geminiContents[] = [
                'role' => $role,
                'parts' => [['text' => $msg['content']]]
            ];
        }
        return $geminiContents;
    }

    /**
     * Interacts with the Gemini API to act as a chat assistant to gather book details.
     */
    public function chatStep(array $history, string $userMessage)
    {
        $apiKey = $this->getApiKey();

        if (empty($apiKey)) {
            throw new \Exception('API Key fehlt. Bitte im Admin-Panel eintragen.');
        }

        $systemPrompt = "Du bist ein freundlicher, kreativer Assistent für eine digitale E-Book-Bibliothek. Deine Aufgabe ist es, zusammen mit dem User ein Buch (ca. 5-10 Kapitel) zu generieren.\n"
            . "Gehe in folgenden Schritten vor:\n"
            . "1. Frage den User nach dem Genre.\n"
            . "2. Frage nach der Hauptfigur (Name, Eigenschaften).\n"
            . "3. Frage ganz kurz nach der grundlegenden Handlung oder dem Ziel der Geschichte.\n"
            . "4. Frage, wie der Titel lauten soll (oder schlage einen vor).\n"
            . "Wenn du alle diese Informationen hast (Genre, Figur, Handlung, Titel), antworte EXAKT mit dem folgenden Schlüsselwort auf einer eigenen Zeile am Ende deiner Nachricht:\n"
            . "[READY_TO_GENERATE]\n"
            . "Dadurch weiß das System, dass es im Hintergrund mit der Buch-Generierung beginnen kann. Teile dem User mit, dass er einen Moment Geduld haben soll.";

        $contents = $this->mapHistoryToGemini($history);

        // Append new message
        if (!empty($userMessage)) {
            $contents[] = [
                'role' => 'user',
                'parts' => [['text' => $userMessage]]
            ];
        }

        try {
            $response = $this->client->post('gemini-1.5-flash:generateContent?key=' . $apiKey, [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'system_instruction' => [
                        'parts' => ['text' => $systemPrompt]
                    ],
                    'contents' => $contents,
                    'generationConfig' => [
                        'temperature' => 0.7,
                    ]
                ]
            ]);

            $body = json_decode((string)$response->getBody(), true);

            return $body['candidates'][0]['content']['parts'][0]['text'] ?? 'Fehler beim Abrufen der Antwort.';

        }
        catch (\Exception $e) {
            Log::error('AI Chat Error: ' . $e->getMessage());
            throw new \Exception('Verbindungsfehler zur KI. Bitte später erneut versuchen.');
        }
    }

    /**
     * Generates the actual book content based on the gathered details.
     */
    public function generateBook(array $history)
    {
        $apiKey = $this->getApiKey();

        if (empty($apiKey)) {
            throw new \Exception('API Key fehlt.');
        }

        $systemPrompt = "Du bist ein professioneller Bestseller-Autor. Basierend auf dem vorangegangenen Gespräch mit dem User sollst du nun das komplette Buch schreiben.\n"
            . "WICHTIGE REGELN DAFÜR:\n"
            . "1. Der Text darf NUR das Buch selbst enthalten. Kein Vorgeplänkel wie 'Hier ist dein Buch:', kein Nachwort.\n"
            . "2. Schreibe zwischen 5 und 10 Kapiteln.\n"
            . "3. Formatiere die Kapitelüberschriften gut sichtbar (z.B. KAPITEL 1: DER ANFANG).\n"
            . "4. Achte auf packende Beschreibungen, Dialoge und eine gute Länge.\n"
            . "Die Ausgabe MUSS REINER TEXT (plain text) sein, da er direkt als .txt Datei gespeichert wird.";

        $contents = $this->mapHistoryToGemini($history);

        try {
            $response = $this->client->post('gemini-1.5-flash:generateContent?key=' . $apiKey, [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'system_instruction' => [
                        'parts' => ['text' => $systemPrompt]
                    ],
                    'contents' => $contents,
                    'generationConfig' => [
                        'temperature' => 0.8,
                        'maxOutputTokens' => 8192,
                    ]
                ]
            ]);

            $body = json_decode((string)$response->getBody(), true);

            return $body['candidates'][0]['content']['parts'][0]['text'] ?? 'Fehler bei der Buchgenerierung.';

        }
        catch (\Exception $e) {
            Log::error('AI Generation Error: ' . $e->getMessage());
            throw new \Exception('Das Buch konnte nicht generiert werden. Bitte später erneut versuchen.');
        }
    }
}
