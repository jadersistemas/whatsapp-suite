<!DOCTYPE html>
<html lang="pt-BR" class="">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'WhatsApp Manager')</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        // Apply theme immediately to prevent flash
        if (localStorage.getItem('theme') === 'dark' || (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        }
    </script>
    <script>
        tailwind.config = {
            darkMode: 'class',
        }
    </script>
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #25D366 0%, #128C7E 100%);
        }
        .card-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
        }
        .qr-container {
            background: white;
            border: 4px solid #25D366;
            border-radius: 12px;
            padding: 16px;
        }
        .status-pulse {
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
        .dark .qr-container {
            background: #1f2937;
        }
    </style>
</head>
<body class="bg-gray-100 dark:bg-gray-900 min-h-screen transition-colors duration-300">
    <!-- Navigation -->
    <nav class="gradient-bg shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center text-white">
                        <i class="fab fa-whatsapp text-3xl mr-3"></i>
                        <span class="font-bold text-xl">WhatsApp Manager</span>
                    </a>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('dashboard') }}" class="text-white hover:text-green-100 transition">
                        <i class="fas fa-home mr-1"></i> Dashboard
                    </a>
                    <a href="{{ route('whatsapp.instances') }}" class="text-white hover:text-green-100 transition">
                        <i class="fas fa-server mr-1"></i> Instâncias
                    </a>
                    <a href="{{ route('settings') }}" class="text-white hover:text-green-100 transition">
                        <i class="fas fa-cog mr-1"></i> Config
                    </a>

                    <!-- Theme Toggle -->
                    <button onclick="toggleTheme()" class="text-white hover:text-green-100 transition p-2" title="Alternar tema">
                        <i id="theme-icon" class="fas fa-moon text-lg"></i>
                    </button>

                    <form method="POST" action="{{ route('auth.logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-white hover:text-green-100 transition">
                            <i class="fas fa-sign-out-alt mr-1"></i> Sair
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="mb-4 bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-300 px-4 py-3 rounded relative" role="alert">
                <strong class="font-bold">Sucesso!</strong>
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 bg-red-100 dark:bg-red-900 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-300 px-4 py-3 rounded relative" role="alert">
                <strong class="font-bold">Erro!</strong>
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-gray-800 dark:bg-gray-950 text-white py-4 mt-8">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <p class="mb-1">WhatsApp Manager - Baseado em <a href="https://github.com/code-chat-br/whatsapp-api-go" class="text-green-400 hover:underline" target="_blank">whatsapp-api-go</a></p>
            <p class="text-sm text-gray-400">Jáder Oliveira - 88988420622</p>
        </div>
    </footer>

    <script>
        // Initialize theme icon on load
        updateThemeIcon();

        function toggleTheme() {
            const html = document.documentElement;
            if (html.classList.contains('dark')) {
                html.classList.remove('dark');
                localStorage.setItem('theme', 'light');
            } else {
                html.classList.add('dark');
                localStorage.setItem('theme', 'dark');
            }
            updateThemeIcon();
        }

        function updateThemeIcon() {
            const icon = document.getElementById('theme-icon');
            if (document.documentElement.classList.contains('dark')) {
                icon.className = 'fas fa-sun text-lg';
            } else {
                icon.className = 'fas fa-moon text-lg';
            }
        }
    </script>

    @stack('scripts')
</body>
</html>
