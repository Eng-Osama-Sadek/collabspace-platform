<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Collabspace') - Workspace</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 text-slate-800 antialiased">
@auth
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <aside class="w-64 bg-slate-900 text-slate-200 flex flex-col justify-between shrink-0">
            <div>
                <div class="p-5 border-b border-slate-800 flex items-center justify-between">
                    <a href="{{ route('dashboard') }}" class="text-xl font-bold tracking-wide text-white">
                        Collabspace
                    </a>
                    <span class="text-xs px-2 py-0.5 rounded bg-indigo-600 text-white">Laravel</span>
                </div>

                <nav class="p-3 space-y-1 text-sm">
                    <a href="{{ route('dashboard') }}" class="block px-3 py-2 rounded-lg hover:bg-slate-800 {{ request()->routeIs('dashboard') ? 'bg-indigo-600 text-white font-medium' : '' }}">
                        Dashboard
                    </a>
                    <a href="{{ route('projects.index') }}" class="block px-3 py-2 rounded-lg hover:bg-slate-800 {{ request()->routeIs('projects.*') ? 'bg-indigo-600 text-white font-medium' : '' }}">
                        Projects
                    </a>
                    <a href="{{ route('tasks.index') }}" class="block px-3 py-2 rounded-lg hover:bg-slate-800 {{ request()->routeIs('tasks.*') ? 'bg-indigo-600 text-white font-medium' : '' }}">
                        Tasks & Board
                    </a>
                    <a href="{{ route('files.index') }}" class="block px-3 py-2 rounded-lg hover:bg-slate-800 {{ request()->routeIs('files.*') ? 'bg-indigo-600 text-white font-medium' : '' }}">
                        Files
                    </a>
                    <a href="{{ route('chats.index') }}" class="block px-3 py-2 rounded-lg hover:bg-slate-800 {{ request()->routeIs('chats.*') ? 'bg-indigo-600 text-white font-medium' : '' }}">
                        Chats & Channels
                    </a>
                    <a href="{{ route('meetings.index') }}" class="block px-3 py-2 rounded-lg hover:bg-slate-800 {{ request()->routeIs('meetings.*') ? 'bg-indigo-600 text-white font-medium' : '' }}">
                        Meetings
                    </a>
                    <a href="{{ route('reports.index') }}" class="block px-3 py-2 rounded-lg hover:bg-slate-800 {{ request()->routeIs('reports.*') ? 'bg-indigo-600 text-white font-medium' : '' }}">
                        Reports
                    </a>
                    <a href="{{ route('notifications.index') }}" class="block px-3 py-2 rounded-lg hover:bg-slate-800 {{ request()->routeIs('notifications.*') ? 'bg-indigo-600 text-white font-medium' : '' }}">
                        Notifications
                    </a>
                    <a href="{{ route('profile.edit') }}" class="block px-3 py-2 rounded-lg hover:bg-slate-800 {{ request()->routeIs('profile.*') ? 'bg-indigo-600 text-white font-medium' : '' }}">
                        Profile
                    </a>
                </nav>
            </div>

            <div class="p-4 border-t border-slate-800 space-y-3">
                <div class="text-xs">
                    <div class="font-semibold text-white">{{ auth()->user()->name }}</div>
                    <div class="text-slate-400">{{ auth()->user()->job_title }} · {{ auth()->user()->department }}</div>
                    <div class="mt-1 inline-block px-2 py-0.5 rounded bg-emerald-900/60 text-emerald-300 capitalize">
                        {{ auth()->user()->availability_status }}
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left text-xs px-3 py-2 rounded bg-slate-800 hover:bg-rose-600 text-white transition">
                        Logout
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col min-w-0">
            <header class="bg-white border-b border-slate-200 px-6 py-4 flex items-center justify-between">
                <h1 class="text-lg font-bold text-slate-800">@yield('header', 'Workspace')</h1>
                <div class="flex items-center gap-3 text-sm">
                    <span class="text-slate-500">{{ now()->format('D, M d, Y') }}</span>
                    <a href="{{ route('profile.edit') }}" class="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 font-medium">
                        {{ auth()->user()->name }}
                    </a>
                </div>
            </header>

            <main class="p-6 flex-1">
                @if(session('success'))
                    <div class="mb-4 p-3 rounded-lg bg-emerald-100 text-emerald-800 text-sm font-medium">
                        {{ session('success') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="mb-4 p-3 rounded-lg bg-rose-100 text-rose-800 text-sm">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>
@else
    <main class="min-h-screen flex items-center justify-center p-4">
        <div class="w-full max-w-md">
            @if(session('success'))
                <div class="mb-4 p-3 rounded-lg bg-emerald-100 text-emerald-800 text-sm font-medium">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-4 p-3 rounded-lg bg-rose-100 text-rose-800 text-sm">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </div>
    </main>
@endauth
</body>
</html>