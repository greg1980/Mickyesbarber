<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('barber.{barberId}', function ($user, $barberId) {
    return $user->barber && (int) $user->barber->id === (int) $barberId;
});

Broadcast::channel('test-channel', function () {
    return true;
});
