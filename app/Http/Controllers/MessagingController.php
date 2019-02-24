<?php

namespace App\Http\Controllers;

use App\Events\ThreadMessage;
use App\Http\Resources\Message\MessageResource;
use App\Http\Resources\PartialUser\PartialUser;
use App\Http\Resources\Participant\ParticipantResource;
use App\Http\Resources\Thread\ThreadResource;
use App\Models\ContactRequest;
use App\Jobs\ContactRequestJob;
use App\Models\User;
use Carbon\Carbon;
use App\Models\Message;
use App\Models\Participant;
use App\Models\Thread;
//use Cmgmyr\Messenger\Models\Message;
//use Cmgmyr\Messenger\Models\Participant;
//use Cmgmyr\Messenger\Models\Thread;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class MessagingController extends Controller
{
    private $user;

    public function __construct()
    {
        $this->middleware(['auth:api']);
        $this->user = Auth::user();
    }

    public function contacts()
    {
        $users = User::all(['id', 'first_name', 'last_name'])->except($this->user->id);
        return ['data' => PartialUser::collection($users)];
    }

    public function unreadCount() {
        $unread = Message::unreadForUser($this->user->id)->count();
        return ['data' => $unread];
    }

    public function thread(Thread $thread)
    {
        if ($thread->hasParticipant($this->user->id))
        {
            $thread->markAsRead($this->user->id);

            $relations = $this->getThreadRelations([$thread->id]);

            return ['data' => array_merge($relations, ['thread' => new ThreadResource($thread)])];
        }
        else
        {
            // TODO: Return 404
        }
    }

    public function markAsRead(Thread $thread)
    {
        if ($thread->hasParticipant($this->user->id))
        {
            $participant = $thread->markAsReadGetParticipant($this->user->id);

            return ['data' => ['participant' => new ParticipantResource($participant)]];
        }
        else
        {
            // TODO: Return 404
        }
    }

    public function markAsUnread(Thread $thread)
    {
        if ($thread->hasParticipant($this->user->id))
        {
            $participant = $thread->markAsUnreadGetParticipant($this->user->id);

            return ['data' => ['participant' => new ParticipantResource($participant)]];
        }
        else
        {
            // TODO: Return 404
        }
    }

    public function threadReply(Thread $thread, Request $request)
    {
        $request->validate([
            'body' => 'required|string'
        ]);

        if ($thread->hasParticipant($this->user->id))
        {
            $message = Message::create([
                'thread_id' => $thread->id,
                'user_id' => $this->user->id,
                'body' => $request['body']
            ]);

            // Mark the thread as read for current user
            $thread->markAsRead($this->user->id);

            // Get all the participants for the thread
            $participants = $thread->participants;

            $uniqueUserIds = $participants->pluck('user_id')->unique()->toArray();
            $users = User::query()->whereIn('id', $uniqueUserIds)->get();

            // Since we manipulated the thread we need to re
            $thread = $thread->fresh();

            $response = [
                'thread' => (new ThreadResource($thread))->resolve(),
                'message' => (new MessageResource($message))->resolve(),
                'participants' => (ParticipantResource::collection($participants))->resolve(),
                'users' => (PartialUser::collection($users))->resolve()
            ];

            foreach ($uniqueUserIds as $userId) {
                // We don't need to send a socket message to the user who replied
                // As they will receive the same message during the response
                if ($userId !== $this->user->id) {
                    event(new ThreadMessage($userId, $response));
                }
            }

            return [ 'data' => $response ];
        }
        // TODO: Return error as user is not a participant in this thread
        return response()->json();
    }

    private function getThreadRelations($threadIds) {
        $messages = Message::forThreads($threadIds)->get();
        $participants = Participant::forThreads($threadIds)->get();
        $uniqueUserIds = $participants->pluck('user_id')->unique()->toArray();
        $users = User::query()->whereIn('id', $uniqueUserIds)->get();

        return [
            'messages' => MessageResource::collection($messages),
            'participants' => ParticipantResource::collection($participants),
            'users' => PartialUser::collection($users)
        ];
    }
    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function threads(Request $request)
    {
        $threads = Thread::forUser($this->user->id)->get();
        $threadIds = $threads->pluck('id')->toArray();
        $relations = $this->getThreadRelations($threadIds);
        return ['data' => array_merge($relations, ['threads' => ThreadResource::collection($threads)])];
    }

    public function compose(Request $request)
    {
        // TODO: move exist validation to a method where we make sure that
        // TODO: the participants added is part of users' friends list
        $request->validate([
            'participants' => 'required',
            'participants.*.id' => 'required|integer|distinct|exists:users,id',
            'subject' => 'nullable|string',
            'body' => 'required|string'
        ]);

        $subject = $request['subject'] ?? '';
        $body = $request['body'];

        $userIds = array_column($request['participants'], 'id');
        // By default participants are NOT admins
        $isParticipantAdmin = 0;
        // If there is only one other participant
        if (count($userIds) === 1) {
            // We only allow subjects for group conversations
            $subject = '';
            // If there is only one other participant, we will make them admin as well
            $isParticipantAdmin = 1;
            // TODO: We need to check to make sure they don't already have any threads with that user
        }

        $thread = Thread::create([
           'subject' => $subject
        ]);

        $message = Message::create([
           'thread_id' => $thread->id,
           'user_id' => $this->user->id,
           'body' => $body
        ]);

        // Add current user as a new participant
        // Set last read to now as they created it
        $participants = collect();

        $participants->push(Participant::create([
            'thread_id' => $thread->id,
            'user_id' => $this->user->id,
            'last_read' => new Carbon,
            'is_admin' => 1
        ]));


        foreach ($userIds as $userId) {
            $participants->push(Participant::create([
                'thread_id' => $thread->id,
                'user_id' => $userId,
                'is_admin' => $isParticipantAdmin
            ]));
        }

        $uniqueUserIds = $participants->pluck('user_id')->unique()->toArray();
        $users = User::query()->whereIn('id', $uniqueUserIds)->get();

        $response = [
            'thread' => (new ThreadResource($thread))->resolve(),
            'message' => (new MessageResource($message))->resolve(),
            'participants' => (ParticipantResource::collection($participants))->resolve(),
            'users' => (PartialUser::collection($users))->resolve()
        ];

        foreach ($userIds as $userId) {
            event(new ThreadMessage($userId, $response));
        }

        return [ 'data' => $response ];
    }
}
