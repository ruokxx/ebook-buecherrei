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
            'base_uri' => 'https://api.openai.com/v1/',
            'timeout' => 120, // Book generation can take time
        ]);
    }

    /**
     * Helper to get the API Key.
     */
    protected function getApiKey()
    {
        return Setting::get('llm_api_key', config('services.openai.key', env('OPENAI_API_KEY')));
    }

    /**
     * Interacts with the OpenAI API to act as a chat assistant to gather book details.
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

        $messages = [
            ['role' => 'system', 'content' => $systemPrompt]
        ];

        // Append history
        foreach ($history as $msg) {
            $messages[] = $msg;
        }

        // Append new message
        if (!empty($userMessage)) {
            $messages[] = ['role' => 'user', 'content' => $userMessage];
        }

        try {
            $response = $this->client->post('chat/completions', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $apiKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'model' => 'gpt-4o-mini', // Fast, cheap, capable enough
                    'messages' => $messages,
                    'temperature' => 0.7,
                ]
            ]);

            $body = json_decode((string)$response->getBody(), true);

            return $body['choices'][0]['message']['content'] ?? 'Fehler beim Abrufen der Antwort.';

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

        $messages = [
            ['role' => 'system', 'content' => $systemPrompt]
        ];

        foreach ($history as $msg) {
            $messages[] = $msg;
        }

        try {
            $response = $this->client->post('chat/completions', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $apiKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'model' => 'gpt-4o', // Use a stronger model for actual book generation if desired, or stick to mini
                    'messages' => $messages,
                    'temperature' => 0.8, // Slightly higher for creativity
                    'max_tokens' => 4000, // Make sure it can output a lot of text
                ]
            ]);

            $body = json_decode((string)$response->getBody(), true);

            return $body['choices'][0]['message']['content'] ?? 'Fehler bei der Buchgenerierung.';

        }
        catch (\Exception $e) {
            Log::error('AI Generation Error: ' . $e->getMessage());
            throw new \Exception('Das Buch konnte nicht generiert werden. Bitte später erneut versuchen.');
        }
    }
}
