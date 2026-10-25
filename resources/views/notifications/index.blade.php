@extends('layouts.portal')

@section('title', 'Notifications')
@section('subtitle', 'Updates about your jobs, applications and account')

@section('content')
    @php
        $user = auth()->user();
        $unreadCount = $user->unreadNotifications()->count();
    @endphp

    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h2 class="font-display text-lg font-semibold text-brand-900">Your notifications</h2>
            <p class="mt-1 text-sm text-slate-600">{{ $unreadCount }} unread</p>
        </div>

        @if($unreadCount > 0)
            <form method="POST" action="{{ route('notifications.read-all') }}">
                @csrf
                <button type="submit" class="inline-flex min-h-[44px] items-center justify-center rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                    Mark all as read
                </button>
            </form>
        @endif
    </div>

    <div class="mt-6 overflow-hidden rounded-xl border border-slate-200 bg-white">
        @forelse($notifications as $notification)
            <a href="{{ route('notifications.read', $notification->id) }}" class="flex gap-4 border-b border-slate-100 px-4 py-4 transition-colors last:border-0 hover:bg-slate-50 sm:px-6 {{ $notification->read_at ? '' : 'bg-brand-50/60' }}">
                <span class="mt-2 h-2.5 w-2.5 shrink-0 rounded-full {{ match($notification->data['level'] ?? 'info') {
                    'success' => 'bg-green-500',
                    'warning' => 'bg-amber-500',
                    'danger' => 'bg-red-500',
                    default => 'bg-brand-500',
                } }}"></span>

                <span class="min-w-0 flex-1">
                    <span class="flex flex-wrap items-center justify-between gap-2">
                        <span class="font-semibold text-slate-900">{{ $notification->data['title'] ?? 'Notification' }}</span>
                        @unless($notification->read_at)
                            <x-badge variant="gold">New</x-badge>
                        @endunless
                    </span>
                    <span class="mt-1 block text-sm leading-relaxed text-slate-600">{{ $notification->data['message'] ?? '' }}</span>
                    <span class="mt-2 block text-xs text-slate-400">{{ $notification->created_at->format('M j, Y \a\t g:ia') }} · {{ $notification->created_at->diffForHumans() }}</span>
                </span>

                <x-icon name="chevron-right" class="mt-1 h-4 w-4 shrink-0 text-slate-400" />
            </a>
        @empty
            <div class="px-6 py-16 text-center">
                <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-brand-50 text-brand-700">
                    <x-icon name="bell" class="h-6 w-6" />
                </span>
                <h3 class="mt-4 font-display text-lg font-semibold text-brand-900">No notifications yet</h3>
                <p class="mt-1 text-sm text-slate-600">We'll let you know when your jobs, applications or account are updated.</p>
            </div>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $notifications->links() }}
    </div>
@endsection
