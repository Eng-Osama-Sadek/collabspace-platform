@extends('layouts.app')

@section('title', 'Sign In')

@section('content')
<div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-7">
    <div class="mb-6 text-center">
        <h2 class="text-2xl font-bold text-slate-900">Collabspace</h2>
        <p class="text-sm text-slate-500 mt-1">Sign in to manage projects, tasks, and teams</p>
    </div>

    <form method="POST" action="{{ route('login.submit') }}" class="space-y-4">
        @csrf

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Email</label>
            <input type="email" name="email" value="{{ old('email', 'admin@collabspace.local') }}" required
                   class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:border-indigo-600">
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Password</label>
            <input type="password" name="password" required
                   class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:border-indigo-600">
        </div>

        <div class="flex items-center justify-between text-sm">
            <label class="inline-flex items-center gap-2 text-slate-600">
                <input type="checkbox" name="remember" value="1" class="rounded border-slate-300">
                <span>Remember me</span>
            </label>
            <span class="text-xs text-slate-400">Demo pass: password123</span>
        </div>

        <button type="submit" class="w-full py-2.5 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm transition">
            Sign In
        </button>
    </form>

    <p class="mt-5 text-center text-sm text-slate-600">
        Don't have an account?
        <a href="{{ route('register') }}" class="text-indigo-600 font-semibold hover:underline">Sign Up</a>
    </p>
</div>
@endsection