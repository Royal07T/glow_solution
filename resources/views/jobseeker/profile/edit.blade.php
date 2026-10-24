@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-3xl font-bold text-purple-900 mb-8">Edit Profile</h1>
    <div class="bg-white p-6 rounded-lg shadow-md">
        <form method="POST" action="{{ route('jobseeker.profile.update') }}">
            @csrf
            @method('PUT')
            <div class="grid md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">First Name</label>
                    <input type="text" name="first_name" value="{{ $profile->first_name }}" class="w-full border border-gray-300 rounded-md px-4 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Last Name</label>
                    <input type="text" name="last_name" value="{{ $profile->last_name }}" class="w-full border border-gray-300 rounded-md px-4 py-2">
                </div>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Bio</label>
                <textarea name="bio" rows="4" class="w-full border border-gray-300 rounded-md px-4 py-2">{{ $profile->bio }}</textarea>
            </div>
            <div class="grid md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">City</label>
                    <input type="text" name="city" value="{{ $profile->city }}" class="w-full border border-gray-300 rounded-md px-4 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Country</label>
                    <input type="text" name="country" value="{{ $profile->country }}" class="w-full border border-gray-300 rounded-md px-4 py-2">
                </div>
            </div>
            <button type="submit" class="bg-purple-900 text-white px-6 py-2 rounded-md hover:bg-purple-800">Update Profile</button>
        </form>
    </div>
</div>
@endsection
