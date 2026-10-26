@auth
    @php
        $absoluteLifetime = max(0, (int) config('session.absolute_lifetime', 0));
        $warningWindow = max(0, (int) config('session.timeout_warning', 0));
        $startedAt = (int) session(\App\Http\Middleware\EnforceSessionTimeout::STARTED_AT_KEY, now()->timestamp);
        $remaining = max(0, $startedAt + ($absoluteLifetime * 60) - now()->timestamp);
    @endphp

    @if ($absoluteLifetime > 0)
        <div
            data-session-timeout
            data-remaining="{{ $remaining }}"
            data-warning="{{ $warningWindow * 60 }}"
            class="fixed bottom-4 right-4 z-[70] hidden w-[calc(100%-2rem)] max-w-sm rounded-xl border border-amber-300 bg-white p-4 shadow-xl"
            role="status"
            aria-live="polite"
        >
            <p class="text-sm font-semibold text-slate-900">Session expiring soon</p>
            <p class="mt-1 text-sm text-slate-600">
                For your security, you will be signed out in
                <span data-session-timeout-countdown class="font-semibold text-amber-700">{{ sprintf('%d:%02d', intdiv($remaining, 60), $remaining % 60) }}</span>.
            </p>
            <button type="button" data-session-timeout-signout class="mt-3 inline-flex items-center justify-center rounded-lg bg-brand-700 px-3 py-2 text-sm font-semibold text-white transition-colors hover:bg-brand-600">
                Sign out now
            </button>
        </div>

        <form method="POST" action="{{ route('logout') }}" data-session-timeout-form class="hidden">
            @csrf
            <input type="hidden" name="reason" value="timeout">
        </form>
    @endif
@endauth
