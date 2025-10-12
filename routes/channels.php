<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('chat.admin', function ($user) {
    if (! $user) {
        return false;
    }

    $isAdmin = false;

    if (method_exists($user, 'hasRole')) {
        $isAdmin = $user->hasRole('admin');
    }

    if (! $isAdmin && property_exists($user, 'is_admin')) {
        $isAdmin = (bool) $user->is_admin;
    }

    if ($isAdmin) {
        return [
            'id' => $user->id,
            'name' => $user->name,
        ];
    }

    return false;
});

Broadcast::channel('chat.user.{userId}', function ($user, int $userId) {
    if (! $user) {
        return false;
    }

    if ((int) $user->id === $userId) {
        return true;
    }

    $isAdmin = false;

    if (method_exists($user, 'hasRole')) {
        $isAdmin = $user->hasRole('admin');
    }

    if (! $isAdmin && property_exists($user, 'is_admin')) {
        $isAdmin = (bool) $user->is_admin;
    }

    if ($isAdmin) {
        return true;
    }

    return false;
});
