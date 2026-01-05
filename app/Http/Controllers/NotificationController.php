<?php

namespace App\Http\Controllers;
use App\Models\SidiaApproval;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // ambil semua notifikasi user login (terbaru dulu)
        // $notifications = $user->notifications
        //     ->sortByDesc('created_at');
        $notifications = auth()->user()
        ->notifications()
        ->orderByRaw('read_at IS NULL DESC')
        ->orderByDesc('created_at')
        ->get();

        return view('notifications.index', compact('notifications'));
    }

    public function show($id)
    {
        $notification = auth()->user()
            ->notifications()
            ->where('id', $id)
            ->firstOrFail();

        // auto mark as read
        if (is_null($notification->read_at)) {
            $notification->markAsRead();
        }

        $approval = null;

        if (!empty($notification->data['approval_no'])) {
            $approval = SidiaApproval::where(
                'approval_no',
                $notification->data['approval_no']
            )->first();
        }

        return view('notifications.show', compact('notification', 'approval'));
    }

    public function read($id)
    {
        $notification = auth()->user()
            ->notifications
            ->where('id', $id)
            ->firstOrFail();

        $notification->markAsRead();

        // redirect ke URL tujuan notifikasi
        return redirect($notification->data['url'] ?? url('/'));
    }

    public function readAll()
    {
        auth()->user()
            ->unreadNotifications
            ->each
            ->markAsRead();

        return back()->with('success', 'Semua notifikasi ditandai sudah dibaca');
    }
}
