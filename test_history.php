<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $systemPrompt = "Du bist ein KI Assistent.";

    $contents = [];
    $history = [
        ['role' => 'user', 'content' => 'Sci Fi'],
        ['role' => 'assistant', 'content' => 'Wie heißt die Figur?']
    ];
    $geminiContents = [];
    foreach ($history as $msg) {
        $role = $msg['role'] === 'assistant' ? 'model' : 'user';
        $geminiContents[] = [
            'role' => $role,
            'parts' => [['text' => $msg['content']]]
        ];
    }

    $contents = $geminiContents;
    $contents[0]['parts'][0]['text'] = "SYSTEM:\n" . $systemPrompt . "\n\n" . $contents[0]['parts'][0]['text'];

    $contents[] = [
        'role' => 'user',
        'parts' => [['text' => 'Bob']]
    ];

    $client = new \GuzzleHttp\Client(['verify' => false]);
    $apiKey = trim(\App\Models\Setting::get('llm_api_key'));

    $response = $client->post('https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=' . $apiKey, [
        'headers' => ['Content-Type' => 'application/json'],
        'json' => ['contents' => $contents]
    ]);

    file_put_contents('err.txt', "SUCCESS: " . $response->getBody());
}
catch (\GuzzleHttp\Exception\ClientException $e) {
    if ($e->hasResponse()) {
        file_put_contents('err.txt', "GUZZLE 4xx: " . $e->getResponse()->getBody()->getContents());
    }
}
catch (\Exception $e) {
    file_put_contents('err.txt', "ERROR: " . $e->getMessage());
}
