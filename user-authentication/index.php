<?php
require_once __DIR__ . '/domain/User.php';

$user = new User('1', 'john.doe@example.com', 'John Doe');
print_r($user);

echo "Is user active? " . ($user->isActive() ? 'Yes' : 'No') . PHP_EOL;

echo "User ID: " . $user->id . PHP_EOL;

echo "User Email: " . $user->email . PHP_EOL;