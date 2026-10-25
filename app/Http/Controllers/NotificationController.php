<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $notifications = $request->user()
            ->notifications()
            ->latest()
            ->paginate(20);

        return view('notifications.index', compact('notifications'));
    }

    public function read(Request $request, string $notification)
    {
        $notification = $request->user()
            ->notifications()
            ->findOrFail($notification);

        $notification->markAsRead();

        $url = $notification->data['url'] ?? null;

        if (is_string($url)) {
            $path = parse_url($url, PHP_URL_PATH);
            $host = parse_url($url, PHP_URL_HOST);
            $appHost = parse_url((string) config('app.url'), PHP_URL_HOST);
            $query = parse_url($url, PHP_URL_QUERY);

            if (is_string($path) && str_starts_with($path, '/') && ($host === null || $host === $appHost)) {
                return redirect($path.($query ? '?'.$query : ''));
            }
        }

        return redirect()->route('notifications.index');
    }

    public function markAllRead(Request $request)
    {
        $request->user()->unreadNotifications()->update(['read_at' => now()]);

        return back()->with('success', 'All notifications marked as read.');
    }
}
