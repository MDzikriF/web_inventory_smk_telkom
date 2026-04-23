<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$admins = App\Models\User::where('role', 'admin')->get();
foreach ($admins as $a) {
    echo "Admin nip={$a->nip} name={$a->name}\n";
}

$notes = App\Models\Notification::orderBy('id', 'desc')->take(10)->get();
foreach ($notes as $note) {
    echo "note: id={$note->id} user_id={$note->user_id} title={$note->title} read=" . ((int) $note->is_read) . "\n";
}
