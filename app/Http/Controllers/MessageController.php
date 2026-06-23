<?php
namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    // Employee message list
   public function index()
{
    $user = Auth::user();

    // Get messages for this user that are not deleted for user,
    // and either user message or admin message exists (or both)
    $messages = Message::where('user_id', $user->id)
        ->where('deleted_for_user', false)
        ->where(function($query) {
            $query->whereNotNull('message')
                  ->orWhereNotNull('admin_message');
        })
        ->latest()
        ->get();

    return view('chat.index', compact('messages'));
}

    // Store new employee message
    public function store(Request $request)
    {
        $request->validate([
            // 'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        Message::create([
            'user_id' => Auth::id(),
            // 'subject' => $request->subject,
            'message' => $request->message,
            'status' => 'unread',
        ]);

        return redirect()->route('chat.index')->with('success', __('Message sent!'));
    }

    // Admin message list
   public function adminIndex()
{
    $user = Auth::user();

    if (!$user->planHasFeature('messages')) {
        return redirect()->route('admin.subscription.my')
            ->with('error', __('Messages are not available on your current plan.'));
    }

    $messages = Message::with('user')
        ->where('deleted_for_admin', false)
        ->where('status', '!=', 'pending')
        ->latest()
        ->get();

    return view('admin.messages.index', compact('messages'));
}


    // Admin reply to message
    public function adminReply(Request $request, $id)
    {
        $request->validate([
            'admin_reply' => 'required|string|max:1000',
        ]);

        $message = Message::findOrFail($id);

        if (!Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized');
        }

        $message->admin_reply = $request->admin_reply;
        $message->status = 'responded';
        $message->save();

        return redirect()->back()->with('success', __('Reply sent successfully!'));
    }

    // Delete message for user/admin or everyone
    public function delete(Request $request, $id)
    {
        $request->validate([
            'scope' => 'required|in:me,everyone',
        ]);

        $message = Message::findOrFail($id);
        $user = Auth::user();
        $scope = $request->input('scope');

        if ($scope === 'everyone') {
            // Only admin can delete for everyone
            if (!$user->isAdmin()) {
                abort(403, 'Unauthorized');
            }
            $message->delete();
        } else {
            // Soft delete for user/admin
            if ($user->isAdmin()) {
                $message->deleted_for_admin = true;
            } else {
                $message->deleted_for_user = true;
            }
            $message->save();
        }

        return redirect()->back()->with('success', __('Message deleted successfully.'));
    }
    public function adminSend(Request $request)
{
    $user = Auth::user();

    if (!$user->planHasFeature('messages')) {
        return redirect()->route('admin.subscription.my')
            ->with('error', __('Messages are not available on your current plan.'));
    }

    $request->validate([
        'employee_id' => 'required|exists:users,id',
        'message' => 'required|string',
    ]);

    Message::create([
        'user_id' => $request->employee_id,

        'admin_message' => $request->message,
        'status' => 'Pending',
    ]);

    return redirect()->route('admin.messages.sent')->with('success', __('Message sent to employee successfully!'));
}
public function sent()
{
    $admin = auth()->user();

   $sentMessages = Message::whereNotNull('admin_message')
    ->whereHas('user', function ($query) use ($admin) {
        $query->where('admin_id', $admin->id);
    })
    ->latest()
    ->get();


    return view('admin.messages.sent', compact('sentMessages'));
}

}
