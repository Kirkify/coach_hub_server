<?php

namespace App\Models\Traits;

use App\Models\Message;
use App\Models\Participant;
use App\Models\Thread;
use Illuminate\Database\Eloquent\Builder;

trait Messagable {

    /**
     * Message relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     *
     */
    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    /**
     * Participants relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     *
     */
    public function participants()
    {
        return $this->hasMany(Participant::class);
    }

    /**
     * Thread relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     *
     */
    public function threads()
    {
        return $this->belongsToMany(
            Thread::class,
            'participants',
            'user_id',
            'thread_id'
        );
    }
    /**
     * Returns the new messages count for user.
     *
     * @return int
     */
    public function newThreadsCount()
    {
        return $this->threadsWithNewMessages()->count();
    }

    /**
     * Returns the new messages count for user.
     *
     * @return int
     */
    public function unreadMessagesCount()
    {
        return Message::unreadForUser($this->getKey())->count();
    }

    /**
     * Returns all threads with new messages.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function threadsWithNewMessages()
    {
        return $this->threads()
            ->where(function (Builder $q) {
                $q->whereNull('participants.last_read');
                $q->orWhere('threads.updated_at', '>', $this->getConnection()->raw($this->getConnection()->getTablePrefix() . 'participants.last_read'));
            })->get();
    }
}