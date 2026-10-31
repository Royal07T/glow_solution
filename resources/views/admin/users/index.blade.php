@extends('layouts.portal')

@section('title', 'Users')
@section('subtitle', 'Search accounts, review job seeker identity submissions and manage access.')

@section('content')
    <form method="GET" action="{{ route('admin.users.index') }}" class="mb-5 grid gap-3 rounded-xl border border-slate-200 bg-white p-4 sm:grid-cols-2 lg:grid-cols-5">
        <div>
            <label for="search" class="mb-1 block text-xs font-medium text-slate-600">Search</label>
            <input id="search" type="text" name="search" value="{{ request('search') }}" placeholder="Name, email or phone" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
        </div>
        <div>
            <label for="role" class="mb-1 block text-xs font-medium text-slate-600">Role</label>
            <select id="role" name="role" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                <option value="">Any role</option>
                @foreach(['job_seeker' => 'Job seeker', 'employer' => 'Employer', 'admin' => 'Admin'] as $value => $label)
                    <option value="{{ $value }}" @selected(request('role') === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="status" class="mb-1 block text-xs font-medium text-slate-600">Status</label>
            <select id="status" name="status" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                <option value="">Any status</option>
                <option value="active" @selected(request('status') === 'active')>Active</option>
                <option value="suspended" @selected(request('status') === 'suspended')>Suspended</option>
            </select>
        </div>
        <div>
            <label for="identity_status" class="mb-1 block text-xs font-medium text-slate-600">Identity</label>
            <select id="identity_status" name="identity_status" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                <option value="">Any identity status</option>
                @foreach(['unsubmitted' => 'Not submitted', 'pending' => 'Under review', 'approved' => 'Verified', 'rejected' => 'Rejected'] as $value => $label)
                    <option value="{{ $value }}" @selected(request('identity_status') === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex items-end gap-2">
            <button type="submit" class="rounded-lg bg-brand-700 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-600">Filter</button>
            <a href="{{ route('admin.users.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Reset</a>
        </div>
    </form>

    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-5 py-3 font-medium">User</th>
                        <th class="px-5 py-3 font-medium">Role</th>
                        <th class="px-5 py-3 font-medium">Identity</th>
                        <th class="px-5 py-3 font-medium">Status</th>
                        <th class="px-5 py-3 font-medium">Joined</th>
                        <th class="px-5 py-3 text-right font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($users as $user)
                        @php($seekerProfile = $user->jobSeekerProfile)
                        <tr>
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    @if($seekerProfile?->photo_path)
                                        <img src="{{ route('jobseekers.photo', $user) }}" alt="" class="h-9 w-9 rounded-full object-cover">
                                    @endif
                                    <div>
                                        <p class="font-semibold text-slate-900">{{ $user->name }}</p>
                                        <p class="text-xs text-slate-500">{{ $user->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-4">
                                <x-badge variant="brand">{{ ucfirst(str_replace('_', ' ', $user->role)) }}</x-badge>
                            </td>
                            <td class="px-5 py-4">
                                @if($seekerProfile)
                                    <x-badge :variant="$seekerProfile->identityStatusBadgeVariant()">{{ $seekerProfile->identityStatusLabel() }}</x-badge>
                                    @if($seekerProfile->identificationTypeLabel())
                                        <p class="mt-1 text-xs text-slate-500">{{ $seekerProfile->identificationTypeLabel() }}</p>
                                    @endif
                                @else
                                    <span class="text-xs text-slate-400">Not applicable</span>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                <x-badge :variant="$user->is_suspended ? 'danger' : 'success'">{{ $user->is_suspended ? 'Suspended' : 'Active' }}</x-badge>
                            </td>
                            <td class="px-5 py-4 text-slate-500">{{ $user->created_at->format('M j, Y') }}</td>
                            <td class="px-5 py-4 text-right">
                                <a href="{{ route('admin.users.show', $user) }}" class="font-semibold text-brand-700 hover:underline">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-8 text-center text-slate-600">No users match these filters.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6">{{ $users->links() }}</div>
@endsection
