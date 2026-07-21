<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
| A student can only subscribe to their OWN private notification channel —
| this is what makes NewNotificationBroadcast safe to send without leaking
| one student's notifications to another.
*/
Broadcast::channel('student.{studentId}', function ($user, $studentId) {
    return $user && (int) $user->id === (int) $studentId;
});
