@extends('layouts.app')

@section('title', 'Sign Up')

@section('content')
<div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-7">
    <div class="mb-6 text-center">
        <h2 class="text-2xl font-bold text-slate-900">Create Account</h2>
        <p class="text-sm text-slate-500 mt-1">Join your team workspace on Collabspace</p>
    </div>

    <form method="POST" action="{{ route('register.submit') }}" class="space-y-4">
        @csrf

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Full Name</label>
            <input type="text" name="name" value="{{ old('name') }}" required
                   class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" required
                   class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
        </div>

        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Phone</label>
                <input type="text" name="phone" value="{{ old('phone') }}"
                       class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Experience (Yrs)</label>
                <input type="number" name="years_of_experience" min="0" max="50" value="{{ old('years_of_experience', 1) }}"
                       class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
            </div>
        </div>

        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Job Title</label>
                <input type="text" name="job_title" value="{{ old('job_title', 'Developer') }}"
                       class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Team / Track</label>
                <select name="department" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                    @foreach(['UI/UX', 'Front End', 'Back End', 'Graphic Designers'] as $dept)
                        <option value="{{ $dept }}" @selected(old('department') === $dept)>{{ $dept }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Password</label>
            <input type="password" name="password" required
                   class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Confirm Password</label>
            <input type="password" name="password_confirmation" required
                   class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
        </div>

        <button type="submit" class="w-full py-2.5 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm transition">
            Create Account
        </button>
    </form>

    <p class="mt-5 text-center text-sm text-slate-600">
        Already have an account?
        <a href="{{ route('login') }}" class="text-indigo-600 font-semibold hover:underline">Sign In</a>
    </p>
</div>
@endsection