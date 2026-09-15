<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('admin.sos-alerts', function ($user) {
    return (bool) $user->is_admin;
});