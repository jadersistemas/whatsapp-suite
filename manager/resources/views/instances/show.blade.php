@extends('layouts.app')

@section('title', $instance->name . ' - WhatsApp Manager')

@section('content')
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">
                <i class="fab fa-whatsapp mr-2 text-green-600"></i> {{ $instance->name }}
            </h1>
            <p class="text-gray-600 mt-1">{{ $instance->description ?? 'Sem descrição' }}</p>
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('messages.send', $instance->name) }}" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg transition">
                <i class="fas fa-paper-plane mr-2"></i> Enviar Mensagem
            </a>
            <a href="{{ route('webhook.settings', $instance->name) }}" class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded-lg transition">
                <i class="fas fa-link mr-2"></i> Webhook
            </a>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    {{-- Connection Status --}}
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-bold text-gray-800">
                <i class="fas fa-wifi mr-2 text-green-600"></i> Status da Conexão
            </h2>
        </div>

        <div class="p-6">
            <div class="text-center mb-6">
                <span class="px-4 py-2 text-lg font-bold rounded-full {{ $instance->status_badge }}">
                    {{ $instance->status }}
                </span>
                @if($instance->owner_jid)
                    <p class="mt-2 text-sm text-gray-500">{{ $instance->owner_jid }}</p>
                @endif
            </div>

            @if(in_array($instance->status, ['open', 'OPEN', 'ONLINE']))
                {{-- Connected State --}}
                <div class="text-center py-6">
                    <div class="inline-flex items-center justify-center w-20 h-20 bg-green-100 rounded-full mb-4">
                        <i class="fas fa-check-circle text-green-600 text-4xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-green-700 mb-2">Instância Conectada</h3>
                    <p class="text-gray-500 mb-4">WhatsApp conectado e pronto para uso</p>
                    @if($instance->phone)
                        <p class="text-sm text-gray-500">{{ $instance->phone }}</p>
                    @endif
                </div>
            @else
                {{-- QR Code Section --}}
                <div id="qr-section" class="text-center">
                    <button onclick="connectQR()" class="bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-lg transition mb-4">
                        <i class="fas fa-qrcode mr-2"></i> Conectar via QR Code
                    </button>

                    <div id="qr-loading" class="hidden">
                        <i class="fas fa-spinner fa-spin text-4xl text-green-600"></i>
                        <p class="mt-2 text-gray-600">Gerando QR Code...</p>
                    </div>

                    <div id="qr-result" class="hidden mt-4">
                        <div class="qr-container inline-block">
                            <img id="qr-image" src="" alt="QR Code" class="max-w-xs">
                        </div>
                        <p id="qr-code-text" class="mt-2 text-lg font-mono text-center text-gray-700 break-all"></p>
                        <p class="mt-4 text-sm text-gray-500">Escaneie com o WhatsApp no seu celular</p>
                    </div>
                </div>

                {{-- Pairing Code Section --}}
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <h3 class="font-bold text-gray-800 mb-4">
                        <i class="fas fa-mobile-alt mr-2"></i> Conectar via Código de Pareamento
                    </h3>

                    <form onsubmit="connectPairing(event)">
                        <div class="flex gap-2">
                            <input type="text" id="pairing-phone" placeholder="5511999999999"
                                class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                required>
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition">
                                <i class="fas fa-link mr-2"></i> Conectar
                            </button>
                        </div>
                    </form>

                    <div id="pairing-loading" class="hidden mt-4 text-center">
                        <i class="fas fa-spinner fa-spin text-2xl text-blue-600"></i>
                        <p class="mt-2 text-gray-600">Gerando código de pareamento...</p>
                    </div>

                    <div id="pairing-result" class="hidden mt-4 text-center">
                        <p class="text-gray-600 mb-2">Código de pareamento:</p>
                        <p id="pairing-code" class="text-3xl font-mono font-bold text-blue-600"></p>
                    </div>
                </div>
            @endif

            {{-- Actions --}}
            <div class="mt-6 pt-6 border-t border-gray-200 space-y-2">
                <button onclick="refreshStatus()" class="w-full bg-gray-100 hover:bg-gray-200 text-gray-800 font-bold py-2 px-4 rounded-lg transition">
                    <i class="fas fa-sync mr-2"></i> Atualizar Status
                </button>

                <form action="{{ route('whatsapp.logout', $instance->name) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja desconectar?')">
                    @csrf
                    <button type="submit" class="w-full bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded-lg transition">
                        <i class="fas fa-sign-out-alt mr-2"></i> Desconectar
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Instance Details --}}
    <div class="space-y-6">
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-bold text-gray-800">
                    <i class="fas fa-info-circle mr-2 text-blue-600"></i> Detalhes
                </h2>
            </div>

            <div class="p-6 space-y-4">
                <div class="flex justify-between items-center py-2 border-b">
                    <span class="text-gray-600">Nome</span>
                    <span class="font-medium">{{ $instance->name }}</span>
                </div>
                <div class="flex justify-between items-center py-2 border-b">
                    <span class="text-gray-600">Telefone</span>
                    <span class="font-medium">{{ $instance->phone ?? '-' }}</span>
                </div>
                <div class="flex justify-between items-center py-2 border-b">
                    <span class="text-gray-600">JID</span>
                    <span class="font-medium text-sm">{{ $instance->owner_jid ?? '-' }}</span>
                </div>
                <div class="flex justify-between items-center py-2 border-b">
                    <span class="text-gray-600">Criado em</span>
                    <span class="font-medium">{{ $instance->created_at->format('d/m/Y H:i') }}</span>
                </div>
                <div class="flex justify-between items-center py-2">
                    <span class="text-gray-600">Atualizado em</span>
                    <span class="font-medium">{{ $instance->updated_at->format('d/m/Y H:i') }}</span>
                </div>
            </div>
        </div>

        {{-- Quick Send --}}
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-bold text-gray-800">
                    <i class="fas fa-paper-plane mr-2 text-green-600"></i> Envio Rápido
                </h2>
            </div>

            <div class="p-6">
                <form action="{{ route('messages.text', $instance->name) }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Número</label>
                        <input type="text" name="number" placeholder="5511999999999"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                            required>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Mensagem</label>
                        <textarea name="text" rows="3" placeholder="Digite sua mensagem..."
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                            required></textarea>
                    </div>
                    <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg transition">
                        <i class="fas fa-paper-plane mr-2"></i> Enviar
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const instanceName = '{{ $instance->name }}';

    async function connectQR() {
        document.getElementById('qr-loading').classList.remove('hidden');
        document.getElementById('qr-result').classList.add('hidden');

        try {
            const response = await fetch(`/instances/${instanceName}/connect/qr`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                },
            });

            const data = await response.json();

            if (!response.ok) {
                alert('Erro da API: ' + (data.error || data.message || JSON.stringify(data)));
                return;
            }

            if (data.base64) {
                document.getElementById('qr-image').src = data.base64;
                document.getElementById('qr-result').classList.remove('hidden');
            } else if (data.code) {
                document.getElementById('qr-code-text').textContent = data.code;
                document.getElementById('qr-result').classList.remove('hidden');
            } else if (data.alreadyConnecting) {
                document.getElementById('qr-loading').innerHTML = `
                    <div class="text-center">
                        <i class="fas fa-link text-4xl text-blue-600 mb-2"></i>
                        <p class="text-blue-600 font-bold">Instância já está conectando...</p>
                        <p class="text-gray-500 text-sm mt-1">O QR anterior pode ter expirado. Use o código de pareamento abaixo ou aguarde e tente novamente.</p>
                    </div>`;
            } else {
                alert('Resposta inesperada: ' + JSON.stringify(data));
            }
        } catch (error) {
            alert('Erro ao gerar QR Code: ' + error.message);
        } finally {
            setTimeout(() => {
                document.getElementById('qr-loading').classList.add('hidden');
                document.getElementById('qr-loading').innerHTML = '<i class="fas fa-spinner fa-spin text-4xl text-green-600"></i><p class="mt-2 text-gray-600">Gerando QR Code...</p>';
            }, 3000);
        }
    }

    async function connectPairing(e) {
        e.preventDefault();
        const phone = document.getElementById('pairing-phone').value;

        document.getElementById('pairing-loading').classList.remove('hidden');
        document.getElementById('pairing-result').classList.add('hidden');

        try {
            const response = await fetch(`/instances/${instanceName}/connect/pairing`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ phone }),
            });

            const data = await response.json();

            if (data.code) {
                document.getElementById('pairing-code').textContent = data.code;
                document.getElementById('pairing-result').classList.remove('hidden');
            }
        } catch (error) {
            alert('Erro ao gerar código de pareamento');
        } finally {
            document.getElementById('pairing-loading').classList.add('hidden');
        }
    }

    async function refreshStatus() {
        try {
            const response = await fetch(`/instances/${instanceName}/connection-state`);
            const data = await response.json();
            location.reload();
        } catch (error) {
            alert('Erro ao atualizar status');
        }
    }

    // Auto-refresh status every 30 seconds
    setInterval(refreshStatus, 30000);
</script>
@endpush
