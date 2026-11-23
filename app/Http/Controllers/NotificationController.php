<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Tandai sebuah notifikasi telah dibaca dan redirect ke target URL.
     */
    public function markAsRead(Request $request, string $id): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        $notification = $user->notifications()->where('id', $id)->first();

        if ($notification) {
            $notification->markAsRead();
            $actionUrl = $notification->data['action_url'] ?? null;

            if ($actionUrl) {
                return redirect()->to($actionUrl);
            }
        }

        return back();
    }

    /**
     * Tandai semua notifikasi milik user sebagai telah dibaca.
     */
    public function markAllAsRead(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        $user->unreadNotifications->markAsRead();

        return back()->with('success', 'Semua notifikasi berhasil ditandai telah dibaca.');
    }
}
