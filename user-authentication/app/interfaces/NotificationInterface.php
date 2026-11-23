<?php

namespace App\Interfaces;

interface NotificationInterface
{
    public function sendNotification($userId, $message);
}