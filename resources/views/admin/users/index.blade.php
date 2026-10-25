@extends('layouts.portal')

@section('title', 'Users')
@section('subtitle', 'Search, review and suspend platform accounts.')

@section('content')
    <form method="GET" action="{{ route('admin.users.index') }}" class="mb-5 grid gap-3 rounded-xl border border-stone-200 bg-white p-4 sm:grid-cols-4">
        <div>
            <label for="search" class="mb-1 block text-xs font-medium text-slate-600">Search</label>
            <input id="search" type="text" name="search" value="{{ request('search') }}" placeholder="Name or email" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm">
        </div>
        <div>
            <label for="role" class="mb-1 block text-xs font-medium text-slate-600">Role</label>
            <select id="role" name="role" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm">
                <option value="">Any role</option>
                @foreach(['job_seeker' => 'Job seeker', 'employer' => 'Employer', 'admin' => 'Admin'] as $value => $label)
                    <option value="{{ $value }}" @selected(request('role') === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="status" class="mb-1 block text-xs font-medium text-slate-600">Status</label>
            <select id="status" name="status" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm">
                <option value="">Any status</option>
                <option value="active" @selected(request('status') === 'active')>Active</option>
                <option value="suspended" @selected(request('status') === 'suspended')>Suspended</option>
            </select>
        </div>
        <div class="flex items-end gap-2">
            <button type="submit" class="rounded-lg bg-[#062B52] px-4 py-2 text-sm font-semibold text-white hover:bg-[#07335c]">Filter</button>
            <a href="{{ route('admin.users.index') }}" class="rounded-lg border border-stone-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-stone-50">Reset</a>
        </div>
    </form>

    <div class="overflow-hidden rounded-xl border border-stone-200 bg-white">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-stone-200 text-sm">
                <thead class="bg-stone-50 text-left text-xs uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-5 py-3 font-medium">User</th>
                        <th class="px-5 py-3 font-medium">Role</th>
                        <th class="px-5 py-3 font-medium">Status</th>
                        <th class="px-5 py-3 font-medium">Joined</th>
                        <th class="px-5 py-3 text-right font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @forelse($users as $user)
                        <tr>
                            <td class="px-5 py-4">
                                <p class="font-semibold text-slate-900">{{ $user->name }}</p>
                                <p class="text-xs text-slate-500">{{ $user->email }}</p>
                            </td>
                            <td class="px-5 py-4">
                                <x-badge variant="brand">{{ ucfirst(str_replace('_', ' ', $user->role)) }}</x-badge>
                            </td>
                            <td class="px-5 py-4">
                                <x-badge :variant="$user->is_suspended ? 'danger' : 'success'">{{ $user->is_suspended ? 'Suspended' : 'Active' }}</x-badge>
                            </td>
                            <td class="px-5 py-4 text-slate-500">{{ $user->created_at->format('M j, Y') }}</td>
                            <td class="px-5 py-4 text-right">
                                <a href="{{ route('admin.users.show', $user) }}" class="font-semibold text-[#062B52] hover:underline">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-8 text-center text-slate-600">No users match these filters.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6">{{ $users->links() }}</div>
@endsection
