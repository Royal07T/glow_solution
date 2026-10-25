@extends('layouts.portal')

@section('title', 'Audit log')
@section('subtitle', 'Security-relevant events recorded by the application.')

@section('content')
    <form method="GET" action="{{ route('admin.audit-logs.index') }}" class="mb-5 grid gap-3 rounded-xl border border-stone-200 bg-white p-4 sm:grid-cols-3">
        <div>
            <label for="action" class="mb-1 block text-xs font-medium text-slate-600">Action contains</label>
            <input id="action" type="text" name="action" value="{{ request('action') }}" placeholder="e.g. job.approved" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm">
        </div>
        <div>
            <label for="user" class="mb-1 block text-xs font-medium text-slate-600">User email</label>
            <input id="user" type="text" name="user" value="{{ request('user') }}" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm">
        </div>
        <div class="flex items-end gap-2">
            <button type="submit" class="rounded-lg bg-[#062B52 px-4 py-2 text-sm font-semibold text-white hover:bg-[#5B21B6">Filter</button>
            <a href="{{ route('admin.audit-logs.index') }}" class="rounded-lg border border-stone-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-stone-50">Reset</a>
        </div>
    </form>

    <div class="overflow-hidden rounded-xl border border-stone-200 bg-white">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-stone-200 text-sm">
                <thead class="bg-stone-50 text-left text-xs uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-5 py-3 font-medium">When</th>
                        <th class="px-5 py-3 font-medium">Action</th>
                        <th class="px-5 py-3 font-medium">Actor</th>
                        <th class="px-5 py-3 font-medium">Target</th>
                        <th class="px-5 py-3 font-medium">IP</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @forelse($logs as $log)
                        <tr>
                            <td class="whitespace-nowrap px-5 py-4 text-slate-500">{{ $log->created_at->format('M j, Y g:ia') }}</td>
                            <td class="px-5 py-4">
                                <code class="rounded bg-stone-100 px-1.5 py-0.5 text-xs text-slate-800">{{ $log->action }}</code>
                            </td>
                            <td class="px-5 py-4">
                                @if($log->user)
                                    <a href="{{ route('admin.users.show', $log->user) }}" class="font-medium text-[#062B52 hover:underline">{{ $log->user->name }}</a>
                                @else
                                    <span class="text-slate-400">system</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-slate-600">
                                {{ $log->auditable_type ? class_basename($log->auditable_type).' #'.$log->auditable_id : '—' }}
                            </td>
                            <td class="px-5 py-4 text-slate-500">{{ $log->ip_address }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-8 text-center text-slate-600">No audit events match these filters.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6">{{ $logs->links() }}</div>
@endsection
