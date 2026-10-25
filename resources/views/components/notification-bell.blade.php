@php
    $notificationUser = auth()->user();
    $unreadNotificationCount = $notificationUser->unreadNotifications()->count();
    $recentNotifications = $notificationUser->notifications()->latest()->limit(5)->get();
@endphp

<details data-menu class="relative">
    <summary class="relative flex h-10 w-10 cursor-pointer list-none items-center justify-center rounded-full text-slate-600 transition-colors hover:bg-slate-100 hover:text-brand-700" aria-label="Notifications ({{ $unreadNotificationCount }} unread)">
        <x-icon name="bell" class="h-5 w-5" />
        @if($unreadNotificationCount > 0)
            <span class="absolute -right-0.5 -top-0.5 flex h-4 min-w-4 items-center justify-center rounded-full bg-danger px-1 text-[10px] font-bold leading-none text-white">
                {{ $unreadNotificationCount > 9 ? '9+' : $unreadNotificationCount }}
            </span>
        @endif
    </summary>

    <div class="absolute right-0 z-50 mt-2 w-80 max-w-[calc(100vw-2rem)] overflow-hidden rounded-xl border border-slate-200 bg-white shadow-xl">
        <div class="flex items-center justify-between gap-3 border-b border-slate-100 px-4 py-3">
            <div>
                <p class="text-sm font-semibold text-brand-900">Notifications</p>
                <p class="text-xs text-slate-500">{{ $unreadNotificationCount }} unread</p>
            </div>
            @if($unreadNotificationCount > 0)
                <form method="POST" action="{{ route('notifications.read-all') }}">
                    @csrf
                    <button type="submit" class="text-xs font-semibold text-brand-700 hover:underline">Mark all read</button>
                </form>
            @endif
        </div>

        <div class="max-h-80 overflow-y-auto">
            @forelse($recentNotifications as $notification)
                <a href="{{ route('notifications.read', $notification->id) }}" class="flex gap-3 border-b border-slate-100 px-4 py-3 last:border-0 hover:bg-slate-50 {{ $notification->read_at ? '' : 'bg-brand-50/60' }}">
                    <span class="mt-1.5 h-2 w-2 shrink-0 rounded-full {{ match($notification->data['level'] ?? 'info') {
                        'success' => 'bg-green-500',
                        'warning' => 'bg-amber-500',
                        'danger' => 'bg-red-500',
                        default => 'bg-brand-500',
                    } }}"></span>
                    <span class="min-w-0">
                        <span class="block truncate text-sm font-semibold text-slate-900">{{ $notification->data['title'] ?? 'Notification' }}</span>
                        <span class="mt-0.5 block text-xs leading-relaxed text-slate-600">{{ \Illuminate\Support\Str::limit($notification->data['message'] ?? '', 90) }}</span>
                        <span class="mt-1 block text-[11px] text-slate-400">{{ $notification->created_at->diffForHumans() }}</span>
                    </span>
                </a>
            @empty
                <p class="px-4 py-8 text-center text-sm text-slate-500">You're all caught up.</p>
            @endforelse
        </div>

        <a href="{{ route('notifications.index') }}" class="block border-t border-slate-100 px-4 py-3 text-center text-sm font-semibold text-brand-700 hover:bg-slate-50">View all notifications</a>
    </div>
</details>
