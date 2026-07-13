<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - WhatsApp Manager</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #25D366 0%, #128C7E 100%);
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="w-full max-w-md px-4">
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <!-- Header -->
            <div class="gradient-bg px-8 py-10 text-center">
                <i class="fab fa-whatsapp text-6xl text-white mb-4"></i>
                <h1 class="text-2xl font-bold text-white">WhatsApp Manager</h1>
                <p class="text-green-100 mt-2">Acesse o painel de gerenciamento</p>
            </div>

            <!-- Form -->
            <div class="px-8 py-8">
                @if($error ?? false)
                    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                        <i class="fas fa-exclamation-circle mr-2"></i>{{ $error }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                        <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('auth.apikey.verify') }}">
                    @csrf
                    <div class="mb-6">
                        <label for="api_key" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-key mr-1 text-green-600"></i> API Key
                        </label>
                        <input type="password"
                               id="api_key"
                               name="api_key"
                               required
                               autofocus
                               placeholder="Digite sua API Key"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent outline-none transition">
                    </div>

                    <button type="submit"
                            class="w-full gradient-bg text-white font-bold py-3 px-4 rounded-lg hover:opacity-90 transition flex items-center justify-center">
                        <i class="fas fa-sign-in-alt mr-2"></i> Conectar
                    </button>
                </form>
            </div>
        </div>

        <p class="text-center text-gray-500 text-sm mt-6">
            WhatsApp Manager &copy; {{ date('Y') }}
        </p>
    </div>
</body>
</html>
