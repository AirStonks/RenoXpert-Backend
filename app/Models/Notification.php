<?php

namespace App; // Or App\Models

use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * This model extends Laravel's built-in DatabaseNotification
 * to provide a simple, typed relationship to the User.
 */
class Notification extends DatabaseNotification
{
    /**
     * Get the User who this notification is for.
     *
     * This provides a clean 'user' relationship,
     * assuming your notifiable model is always 'User'.
     *
     * If notifiable can be other models, this should be removed
     * and you should rely on the default `notifiable()` polymorphic relation.
     */
    public function user(): BelongsTo
    {
        // This assumes the `notifiable` is always a User.
        return $this->belongsTo(User::class, 'notifiable_id');
    }
}
