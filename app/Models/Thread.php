<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class Thread extends Model
{
    use SoftDeletes;

    /**
     * The attributes that can be set with Mass Assignment.
     *
     * @var array
     */
    protected $fillable = ['subject'];
    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];
    /**
     * Internal cache for creator.
     *
     * @var null|Models::user()
     */
    protected $creatorCache = null;

    /**
     * Messages relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     *
     * @codeCoverageIgnore
     */
    public function messages()
    {
        return $this->hasMany(Message::class, 'thread_id', 'id');
    }

    public function lastMessage()
    {
        return $this->hasOne(Message::class, 'thread_id', 'id')->latest();
    }

    public function last_read() {
        return $this->hasMany(Participant::class, 'thread_id', 'id')
            ->where('user_id', 1)
            ->select('last_read')->first();
    }

    /**
     * Returns the latest message from a thread.
     *
     * @return null|\App\Models\Message
     */
    public function getLatestMessageAttribute()
    {
        return $this->messages()->latest()->first();
    }
    /**
     * Participants relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     *
     * @codeCoverageIgnore
     */
    public function participants()
    {
        return $this->hasMany(Participant::class, 'thread_id', 'id');
    }

    public function participant()
    {
        return $this->hasOne(Participant::class, 'thread_id', 'id');
    }

    /**
     * User's relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     *
     * @codeCoverageIgnore
     */
    public function users()
    {
        return $this->belongsToMany(User::class, Participant::class, 'thread_id', 'user_id');
    }

    /**
     * Returns the user object that created the thread.
     *
     * @return Models::user()
     */
    public function creator()
    {
        if (is_null($this->creatorCache)) {
            $firstMessage = $this->messages()->withTrashed()->oldest()->first();
            $this->creatorCache = $firstMessage ? $firstMessage->user : Models::user();
        }
        return $this->creatorCache;
    }
    /**
     * Returns all of the latest threads by updated_at date.
     *
     * @return \Illuminate\Database\Query\Builder|static
     */
    public static function getAllLatest()
    {
        return static::latest('updated_at');
    }
    /**
     * Returns all threads by subject.
     *
     * @param string $subject
     *
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public static function getBySubject($subject)
    {
        return static::where('subject', 'like', $subject)->get();
    }
    /**
     * Returns an array of user ids that are associated with the thread.
     *
     * @param null $userId
     *
     * @return array
     */
    public function participantsUserIds($userId = null)
    {
        $users = $this->participants()->withTrashed()->select('user_id')->get()->map(function ($participant) {
            return $participant->user_id;
        });
        if ($userId) {
            $users->push($userId);
        }
        return $users->toArray();
    }

    public function scopeForParticipant(Builder $query, $userId, $participantId)
    {
        return $query->join('participants', 'threads.id', '=', 'participants.thread_id')
            ->where('participants.user_id', $userId)
            ->where('participants.user_id', $participantId)
            ->where('participants.deleted_at', null)
            ->select('threads.*', 'participants.last_read')
//            ->with(['messages' => function($query) {
//                $query->latest();
//            }])
//            ->with(['participants' => function($query) {
//                $query->select('participants.thread_id', 'participants.user_id', 'participants.is_admin');
//                $query->with('user:id,first_name,last_name');
//            }])
            ->first();
    }

    /**
     * Returns threads that the user is associated with.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $userId
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForUser(Builder $query, $userId)
    {
        return $query->join('participants', 'threads.id', '=', 'participants.thread_id')
            ->where('participants.user_id', $userId)
            ->where('participants.deleted_at', null)
            ->select('threads.*', 'participants.last_read')
//            ->with(['messages' => function($query) {
//                $query->latest();
//            }])
//            ->with(['participants' => function($query) {
//                $query->select('participants.thread_id', 'participants.user_id', 'participants.is_admin');
//                $query->with('user:id,first_name,last_name');
//            }])
            ->latest('updated_at');
    }

    /**
     * Returns threads that the user is associated with.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $userId
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForId(Builder $query, $threadId)
    {
        return $query->where('threads.id', $threadId)
            ->join('participants', 'threads.id', '=', 'participants.thread_id')
            ->where('participants.thread_id', $threadId)
            ->where('participants.deleted_at', null)
            ->select('threads.*', 'participants.last_read')
            ->with(['messages' => function($query) {
                $query->latest();
            }])
            ->with(['participants' => function($query) {
                $query->select('participants.thread_id', 'participants.user_id', 'participants.is_admin');
                $query->with('user:id,first_name,last_name');
            }])
            ->latest('updated_at');
    }
    /**
     * Returns threads with new messages that the user is associated with.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $userId
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForUserWithNewMessages(Builder $query, $userId)
    {
        $participantTable = 'participants';
        $messagesTable = 'messages';
        $threadsTable = $this->getTable();
        return $query->join($participantTable, $this->getQualifiedKeyName(), '=', $participantTable . '.thread_id')
            ->where($participantTable . '.user_id', $userId)
            ->whereNull($participantTable . '.deleted_at')
            ->where(function (Builder $query) use ($participantTable, $threadsTable) {
                $query->where($threadsTable . '.updated_at', '>', $this->getConnection()->raw($this->getConnection()->getTablePrefix() . $participantTable . '.last_read'))
                    ->orWhereNull($participantTable . '.last_read');
            })
            ->select($threadsTable . '.*');
             // ->get();
        $participantTable = 'participants';
        $threadsTable = 'threads';
        return $query->join($participantTable, $this->getQualifiedKeyName(), '=', $participantTable . '.thread_id')
            ->where($participantTable . '.user_id', $userId)
            ->whereNull($participantTable . '.deleted_at')
            ->where(function (Builder $query) use ($participantTable, $threadsTable) {
                $query->where($threadsTable . '.updated_at', '>', $this->getConnection()->raw($this->getConnection()->getTablePrefix() . $participantTable . '.last_read'))
                    ->orWhereNull($participantTable . '.last_read');
            })
            ->select($threadsTable . '.*');
    }
    /**
     * Returns threads between given user ids.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array $participants
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeBetween(Builder $query, array $participants)
    {
        return $query->whereHas('participants', function (Builder $q) use ($participants) {
            $q->whereIn('user_id', $participants)
                ->select($this->getConnection()->raw('DISTINCT(thread_id)'))
                ->groupBy('thread_id')
                ->havingRaw('COUNT(thread_id)=' . count($participants));
        });
    }
    /**
     * Add users to thread as participants.
     *
     * @param array|mixed $userId
     *
     * @return void
     */
    public function addParticipant($userId)
    {
        $userIds = is_array($userId) ? $userId : (array) func_get_args();
        collect($userIds)->each(function ($userId) {
            Participant::firstOrCreate([
                'user_id' => $userId,
                'thread_id' => $this->id,
            ]);
        });
    }
    /**
     * Remove participants from thread.
     *
     * @param array|mixed $userId
     *
     * @return void
     */
    public function removeParticipant($userId)
    {
        $userIds = is_array($userId) ? $userId : (array) func_get_args();
        Models::participant()->where('thread_id', $this->id)->whereIn('user_id', $userIds)->delete();
    }
    /**
     * Mark a thread as read for a user.
     *
     * @param int $userId
     *
     * @return void
     */
    public function markAsRead($userId)
    {
        try {
            $participant = $this->getParticipantFromUser($userId);
            $participant->last_read = new Carbon();
            $participant->save();
        } catch (ModelNotFoundException $e) { // @codeCoverageIgnore
            // do nothing
        }
    }

    /**
     * Mark a thread as read for a user.
     *
     * @param int $userId
     *
     * @return Participant
     */
    public function markAsReadGetParticipant($userId)
    {
        try {
            $participant = $this->getParticipantFromUser($userId);
            $participant->last_read = new Carbon();
            $participant->save();
            return $participant;
        } catch (ModelNotFoundException $e) { // @codeCoverageIgnore
            return null;
        }
    }

    // TODO: We should grab the last message from the thread
    // and mark the last_read to just before that.  That way when
    // We fetch the message unread count we only get one unread for that thread
    // Instead of all of them.
    /**
     * Mark a thread as unread for a user.
     *
     * @param int $userId
     *
     * @return Participant
     */
    public function markAsUnreadGetParticipant($userId)
    {
        try {
            $participant = $this->getParticipantFromUser($userId);
            $participant->last_read = null;
            $participant->save();
            return $participant;
        } catch (ModelNotFoundException $e) { // @codeCoverageIgnore
            return null;
        }
    }

    /**
     * See if the current thread is unread by the user.
     *
     * @param int $userId
     *
     * @return bool
     */
    public function isUnread($userId)
    {
        try {
            $participant = $this->getParticipantFromUser($userId);
            if ($participant->last_read === null || $this->updated_at->gt($participant->last_read)) {
                return true;
            }
        } catch (ModelNotFoundException $e) { // @codeCoverageIgnore
            // do nothing
        }
        return false;
    }
    /**
     * Finds the participant record from a user id.
     *
     * @param $userId
     *
     * @return mixed
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function getParticipantFromUser($userId)
    {
        return $this->participants()->where('user_id', $userId)->firstOrFail();
    }
    /**
     * Restores all participants within a thread that has a new message.
     *
     * @return void
     */
    public function activateAllParticipants()
    {
        $participants = $this->participants()->withTrashed()->get();
        foreach ($participants as $participant) {
            $participant->restore();
        }
    }
    /**
     * Generates a string of participant information.
     *
     * @param null|int $userId
     * @param array $columns
     *
     * @return string
     */
    public function participantsString($userId = null, $columns = ['name'])
    {
        $participantsTable = 'participants';
        $usersTable = 'users';
        $userPrimaryKey = Models::user()->getKeyName();
        $selectString = $this->createSelectString($columns);
        $participantNames = $this->getConnection()->table($usersTable)
            ->join($participantsTable, $usersTable . '.' . $userPrimaryKey, '=', $participantsTable . '.user_id')
            ->where($participantsTable . '.thread_id', $this->id)
            ->select($this->getConnection()->raw($selectString));
        if ($userId !== null) {
            $participantNames->where($usersTable . '.' . $userPrimaryKey, '!=', $userId);
        }
        return $participantNames->implode('name', ', ');
    }

    /**
     * Checks to see if a user is a current participant of the thread.
     *
     * @param int $userId
     *
     * @return bool
     */
    public function hasParticipant($userId)
    {
        $participants = $this->participants()->where('user_id', '=', $userId);
        if ($participants->count() > 0) {
            return true;
        }
        return false;
    }
    /**
     * Generates a select string used in participantsString().
     *
     * @param array $columns
     *
     * @return string
     */
    protected function createSelectString($columns)
    {
        $dbDriver = $this->getConnection()->getDriverName();
        $tablePrefix = $this->getConnection()->getTablePrefix();
        $usersTable = Models::table('users');
        switch ($dbDriver) {
            case 'pgsql':
            case 'sqlite':
                $columnString = implode(" || ' ' || " . $tablePrefix . $usersTable . '.', $columns);
                $selectString = '(' . $tablePrefix . $usersTable . '.' . $columnString . ') as name';
                break;
            case 'sqlsrv':
                $columnString = implode(" + ' ' + " . $tablePrefix . $usersTable . '.', $columns);
                $selectString = '(' . $tablePrefix . $usersTable . '.' . $columnString . ') as name';
                break;
            default:
                $columnString = implode(", ' ', " . $tablePrefix . $usersTable . '.', $columns);
                $selectString = 'concat(' . $tablePrefix . $usersTable . '.' . $columnString . ') as name';
        }
        return $selectString;
    }
    /**
     * Returns array of unread messages in thread for given user.
     *
     * @param int $userId
     *
     * @return \Illuminate\Support\Collection
     */
    public function userUnreadMessages($userId)
    {
        $messages = $this->messages()->get();
        try {
            $participant = $this->getParticipantFromUser($userId);
        } catch (ModelNotFoundException $e) {
            return collect();
        }
        if (!$participant->last_read) {
            return $messages;
        }
        return $messages->filter(function ($message) use ($participant) {
            return $message->updated_at->gt($participant->last_read);
        });
    }
    /**
     * Returns count of unread messages in thread for given user.
     *
     * @param int $userId
     *
     * @return int
     */
    public function userUnreadMessagesCount($userId)
    {
        return $this->userUnreadMessages($userId)->count();
    }
}
