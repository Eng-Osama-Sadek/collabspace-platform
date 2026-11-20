@extends('layouts.app')

@section('title', 'Profile')
@section('header', 'My Profile & Availability')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="bg-white rounded-xl border border-slate-200 p-6 space-y-3">
        <div class="text-lg font-bold text-slate-900">{{ $user->name }}</div>
        <div class="text-sm text-slate-500">{{ $user->email }}</div>
        <div class="text-sm text-slate-700">Role: <span class="font-semibold uppercase">{{ $user->role }}</span></div>
        <div class="text-sm text-slate-700">Track: <span class="font-semibold">{{ $user->department }}</span></div>
        <div class="text-sm text-slate-700">Experience: <span class="font-semibold">{{ $user->years_of_experience }} years</span></div>
        <div class="pt-2">
            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 capitalize">
                {{ $user->availability_status }}
            </span>
        </div>
    </div>

    <div class="lg:col-span-2 bg-white rounded-xl border border-slate-200 p-6">
        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Full Name</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                           class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                           class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Phone</label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                           class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Job Title</label>
                    <input type="text" name="job_title" value="{{ old('job_title', $user->job_title) }}" required
                           class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Team / Track</label>
                    <select name="department" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                        @foreach(['UI/UX', 'Front End', 'Back End', 'Graphic Designers'] as $dept)
                            <option value="{{ $dept }}" @selected(old('department', $user->department) === $dept)>{{ $dept }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Years of Experience</label>
                    <input type="number" name="years_of_experience" min="0" max="50"
                           value="{{ old('years_of_experience', $user->years_of_experience) }}" required
                           class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Availability</label>
                    <select name="availability_status" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                        @foreach(['available', 'busy', 'away'] as $status)
                            <option value="{{ $status }}" @selected(old('availability_status', $user->availability_status) === $status)>
                                {{ ucfirst($status) }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Avatar Image</label>
                <input type="file" name="avatar" accept="image/*" class="text-sm text-slate-600">
            </div>

            <button type="submit" class="px-5 py-2.5 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold">
                Save Changes
            </button>
        </form>
    </div>
</div>
@endsection