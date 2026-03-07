<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = \App\Models\User::where('email', 'admin@example.com')->first();
if ($user) {
    echo "Found user: " . $user->email . "\n";
    echo "Password starts with: " . substr($user->password, 0, 10) . "\n";
    $user->update(['is_admin' => true]);
    echo "Set is_admin to true.\n";
}
else {
    echo "User not found. Creating one...\n";
    \App\Models\User::factory()->create([
        'name' => 'Admin',
        'email' => 'admin@example.com',
        'password' => bcrypt('password'),
        'is_admin' => true,
    ]);
    echo "User created and set as admin.\n";
}
