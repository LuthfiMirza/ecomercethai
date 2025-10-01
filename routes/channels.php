<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('chat', function ($user) {
    if (! $user) {
        return false;
    }

    if (method_exists($user, 'hasRole') && ! $user->hasRole('admin')) {
        return false;
    }

    return ['id' => $user->id, 'name' => $user->name];
});
