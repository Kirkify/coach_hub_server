<?php

namespace App\Http\Controllers;

use App\Events\ThreadMessage;
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
        return $users;
    }

    public function thread(Thread $thread)
    {
        if ($thread->hasParticipant($this->user->id))
        {
            $thread->markAsRead($this->user->id);

            $participants = DB::table('participants')
                ->join('users', 'participants.user_id', '=', 'users.id')
                ->where('participants.thread_id', '=', $thread->id)
                ->where('users.id', '!=', $this->user->id)
                ->select('users.id', 'users.first_name', 'users.last_name')
                ->orderBy('users.first_name', 'asc')
                ->get();

            $messages = DB::table('messages')
                ->where('messages.thread_id', $thread->id)
                ->join('users', 'messages.user_id', '=', 'users.id')
                ->select('messages.body', 'messages.created_at', 'users.id', 'users.first_name', 'users.last_name')
                ->orderBy('messages.created_at', 'desc')
                ->get();

            // Carbonize dates
            foreach ($messages as $message) {
                $message->created_at = Carbon::parse($message->created_at)->diffForHumans();
            }

            return response()
                ->json([
                    'thread' => $thread,
                    'messages' => $messages,
                    'participants' => $participants
                ]);
        }
    }

    public function threadReply(Thread $thread, Request $request)
    {
        $request->validate([
            'body' => 'required|string'
        ]);

        if ($thread->hasParticipant($this->user->id))
        {
            Message::create([
                'thread_id' => $thread->id,
                'user_id' => $this->user->id,
                'body' => $request['body']
            ]);

            $thread->markAsRead($this->user->id);
        }

        return response()->json();
    }
    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Laravel\Passport\Http\Controllers\AccessTokenController  $accessTokenController
     * @return \Illuminate\Http\Response
     */
    public function threads(Request $request)
    {
        // return response()->json('Your password was incorrect', 422);
//        $threads = DB::table('threads')
//            ->join('participants', 'threads.id', '=', 'participants.thread_id')
//            ->where('participants.user_id', $this->user->id)
//            ->where('participants.deleted_at', null);
//
//        $participantTable = Models::table('participants');
//        $threadsTable = Models::table('threads');
//
//        return $query->join($participantTable, $this->getQualifiedKeyName(), '=', $participantTable . '.thread_id')
//            ->where($participantTable . '.user_id', $userId)
//            ->whereNull($participantTable . '.deleted_at')
//            ->where(function (Builder $query) use ($participantTable, $threadsTable) {
//                $query->where($threadsTable . '.updated_at', '>', $this->getConnection()->raw($this->getConnection()->getTablePrefix() . $participantTable . '.last_read'))
//                    ->orWhereNull($participantTable . '.last_read');
//            })
//            ->select($threadsTable . '.*');
//
//            //->leftJoin('messages', 'threads.id', '=', 'messages.thread_id')
//            // ->select('threads.id', 'threads.subject', 'participants.last_read')
//            //->orderBy('participants.last_read', 'desc')
//            // ->get();

//        $unreadThreads = Thread::forUserWithNewMessages($this->user->id)
//
//        ->crossJoin('messages', 'threads.id', '=', 'messages.thread_id')
//        ->get();

        // $unreadThreads = Thread::forUserWithNewMessages($this->user->id)->latest('updated_at')->with('latestMessage')->get();
        $threads = \App\Models\Thread::with('latestMessage')->get();
        $threads->map(function ($thread) { return $thread->latestMessage; });
        return;
        $unreadThreads = Thread::with('latestMessage')->get();
        $unreadThreads->map(function ($thread) {
            return $thread->latestMessage;
        });
        return;
        foreach ($unreadThreads as $thread) {
            $latestMessage = $thread->latestMessage;
            $cool = 1;
            // $thread->updated_at = Carbon::parse($thread->updated_at)->diffForHumans();
        }

        $unreadThreadIds = $unreadThreads->pluck('id')->toArray();

        $readThreads = Thread::forUser($this->user->id)->whereNotIn('threads.id', $unreadThreadIds)->latest('updated_at')->get();

        $mergedThreads = $unreadThreads->merge($readThreads);

//        foreach ($mergedThreads as $thread) {
//            $thread->updated_at = Carbon::parse($thread->updated_at)->diffForHumans();
//        }

        return $mergedThreads;
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
        Participant::create([
            'thread_id' => $thread->id,
            'user_id' => $this->user->id,
            'last_read' => new Carbon
        ]);

        $userIds = array_column($request['participants'], 'id');
        $thread->addParticipant($userIds);

        foreach ($request['participants'] as $participant) {
            event(new ThreadMessage($participant["id"], $message, $this->user));
        }

        // event(new ThreadMessage())


        return response()->json();
    }
}
