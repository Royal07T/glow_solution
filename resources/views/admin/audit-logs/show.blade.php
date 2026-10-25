@extends('layouts.portal')

@section('title', 'Audit event')
@section('subtitle', $auditLog->action)

@section('content')
    <div class="space-y-6">
        <section class="rounded-xl border border-slate-200 bg-white p-6">
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Action</p>
                    <code class="mt-1 inline-block rounded bg-slate-100 px-2 py-1 text-sm text-slate-800">{{ $auditLog->action }}</code>
                </div>
                <p class="text-sm text-slate-500">Recorded {{ $auditLog->created_at->format('M j, Y \a\t g:ia') }}</p>
            </div>

            <dl class="mt-6 grid gap-4 sm:grid-cols-2">
                <div>
                    <dt class="text-xs uppercase tracking-wide text-slate-500">Actor</dt>
                    <dd class="mt-1 text-sm font-medium text-slate-800">
                        @if($auditLog->user)
                            <a href="{{ route('admin.users.show', $auditLog->user) }}" class="text-brand-700 hover:underline">{{ $auditLog->user->name }}</a>
                            <span class="block text-xs font-normal text-slate-500">{{ $auditLog->user->email }}</span>
                        @else
                            System
                        @endif
                    </dd>
                </div>
                <div>
                    <dt class="text-xs uppercase tracking-wide text-slate-500">IP address</dt>
                    <dd class="mt-1 text-sm font-medium text-slate-800">{{ $auditLog->ip_address ?: 'Not recorded' }}</dd>
                </div>
                <div>
                    <dt class="text-xs uppercase tracking-wide text-slate-500">Target</dt>
                    <dd class="mt-1 text-sm font-medium text-slate-800">
                        @if($auditLog->auditable_type)
                            @if($targetUrl)
                                <a href="{{ $targetUrl }}" class="text-brand-700 hover:underline">
                                    {{ class_basename($auditLog->auditable_type) }} #{{ $auditLog->auditable_id }}
                                </a>
                            @else
                                {{ class_basename($auditLog->auditable_type) }} #{{ $auditLog->auditable_id }}
                            @endif
                        @else
                            No target record
                        @endif
                    </dd>
                </div>
                <div>
                    <dt class="text-xs uppercase tracking-wide text-slate-500">User agent</dt>
                    <dd class="mt-1 break-words text-sm font-medium text-slate-800">{{ $auditLog->user_agent ?: 'Not recorded' }}</dd>
                </div>
            </dl>
        </section>

        <div class="grid gap-6 lg:grid-cols-2">
            <section class="rounded-xl border border-slate-200 bg-white p-6">
                <h2 class="font-display text-base font-semibold text-slate-900">Previous values</h2>
                @if($auditLog->old_values)
                    <dl class="mt-4 divide-y divide-slate-100 text-sm">
                        @foreach($auditLog->old_values as $key => $value)
                            <div class="grid gap-1 py-3 first:pt-0 last:pb-0 sm:grid-cols-3">
                                <dt class="font-medium text-slate-500">{{ $key }}</dt>
                                <dd class="break-words text-slate-800 sm:col-span-2">
                                    @if(is_scalar($value) || $value === null)
                                        {{ $value === null ? 'null' : (is_bool($value) ? ($value ? 'true' : 'false') : $value) }}
                                    @else
                                        <pre class="whitespace-pre-wrap font-mono text-xs">{{ json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                                    @endif
                                </dd>
                            </div>
                        @endforeach
                    </dl>
                @else
                    <p class="mt-3 text-sm text-slate-600">No previous values were recorded.</p>
                @endif
            </section>

            <section class="rounded-xl border border-slate-200 bg-white p-6">
                <h2 class="font-display text-base font-semibold text-slate-900">New values</h2>
                @if($auditLog->new_values)
                    <dl class="mt-4 divide-y divide-slate-100 text-sm">
                        @foreach($auditLog->new_values as $key => $value)
                            <div class="grid gap-1 py-3 first:pt-0 last:pb-0 sm:grid-cols-3">
                                <dt class="font-medium text-slate-500">{{ $key }}</dt>
                                <dd class="break-words text-slate-800 sm:col-span-2">
                                    @if(is_scalar($value) || $value === null)
                                        {{ $value === null ? 'null' : (is_bool($value) ? ($value ? 'true' : 'false') : $value) }}
                                    @else
                                        <pre class="whitespace-pre-wrap font-mono text-xs">{{ json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                                    @endif
                                </dd>
                            </div>
                        @endforeach
                    </dl>
                @else
                    <p class="mt-3 text-sm text-slate-600">No new values were recorded.</p>
                @endif
            </section>
        </div>

        <a href="{{ route('admin.audit-logs.index') }}" class="inline-flex items-center justify-center rounded-lg bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-200">Back to audit log</a>
    </div>
@endsection
