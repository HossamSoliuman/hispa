#!/usr/bin/env php
<?php

// Assign roles to test users created by ComprehensiveTestSeeder

require __DIR__.'/../../vendor/autoload.php';

$app = require_once __DIR__.'/../../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;

echo "🔧 Assigning roles to test users...\n\n";

// Assign owner role
$owners = User::where('email', 'like', '%@hawat.test')
    ->where('role', 'owner')
    ->get();

foreach ($owners as $user) {
    if (! $user->hasRole('owner')) {
        $user->assignRole('owner');
        echo "✅ Owner role assigned to: {$user->email}\n";
    } else {
        echo "⏭️  {$user->email} already has owner role\n";
    }
}

// Assign captain role
$captains = User::where('email', 'like', '%@hawat.test')
    ->where('role', 'captain')
    ->get();

foreach ($captains as $user) {
    if (! $user->hasRole('captain')) {
        $user->assignRole('captain');
        echo "✅ Captain role assigned to: {$user->email}\n";
    } else {
        echo "⏭️  {$user->email} already has captain role\n";
    }
}

// Assign dalal role
$dalals = User::where('email', 'like', '%@hawat.test')
    ->where('role', 'dalal')
    ->get();

foreach ($dalals as $user) {
    if (! $user->hasRole('dalal')) {
        $user->assignRole('dalal');
        echo "✅ Dalal role assigned to: {$user->email}\n";
    } else {
        echo "⏭️  {$user->email} already has dalal role\n";
    }
}

// Assign counter role
$counters = User::where('email', 'like', '%@hawat.test')
    ->where('role', 'counter')
    ->get();

foreach ($counters as $user) {
    if (! $user->hasRole('counter')) {
        $user->assignRole('counter');
        echo "✅ Counter role assigned to: {$user->email}\n";
    } else {
        echo "⏭️  {$user->email} already has counter role\n";
    }
}

echo "\n✨ Role assignment complete!\n";
echo "\nSummary:\n";
echo '- Owners: '.User::role('owner')->count()."\n";
echo '- Captains: '.User::role('captain')->count()."\n";
echo '- Dalals: '.User::role('dalal')->count()."\n";
echo '- Counters: '.User::role('counter')->count()."\n";
