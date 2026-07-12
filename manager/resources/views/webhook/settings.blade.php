@extends('layouts.app')

@section('title', 'Webhook - ' . $instance->name)

@section('content')
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">
                <i class="fas fa-link mr-2 text-purple-600"></i> Configurar Webhook
            </h1>
            <p class="text-gray-600 mt-1">Instância: <strong>{{ $instance->name }}</strong></p>
        </div>
        <a href="{{ route('whatsapp.show', $instance->name) }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg transition">
            <i class="fas fa-arrow-left mr-2"></i> Voltar
        </a>
    </div>
</div>

<div class="max-w-2xl">
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="bg-purple-600 px-6 py-4">
            <h2 class="text-white font-bold text-lg">
                <i class="fas fa-cog mr-2"></i> Configurações do Webhook
            </h2>
        </div>

        <form action="{{ route('webhook.update', $instance->name) }}" method="POST" class="p-6">
            @csrf
            @method('PUT')

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    URL do Webhook <span class="text-red-500">*</span>
                </label>
                <input type="url" name="url" value="{{ $webhook['data']['url'] ?? '' }}"
                    placeholder="https://seu-servidor.com/webhook"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                    required>
                <p class="mt-1 text-sm text-gray-500">URL que receberá as notificações de eventos</p>
            </div>

            <div class="mb-6">
                <label class="flex items-center">
                    <input type="checkbox" name="enabled" value="1"
                        {{ ($webhook['data']['enabled'] ?? false) ? 'checked' : '' }}
                        class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                    <span class="ml-2 text-sm text-gray-700">Habilitar webhook</span>
                </label>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-3">Eventos</label>

                <div class="space-y-3">
                    <label class="flex items-center">
                        <input type="checkbox" name="events[qrcodeUpdated]" value="1"
                            {{ ($webhook['data']['events']['qrcodeUpdated'] ?? false) ? 'checked' : '' }}
                            class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                        <span class="ml-2 text-sm text-gray-700">
                            <i class="fas fa-qrcode mr-1 text-green-600"></i>
                            QR Code Atualizado
                        </span>
                    </label>

                    <label class="flex items-center">
                        <input type="checkbox" name="events[connectionUpdated]" value="1"
                            {{ ($webhook['data']['events']['connectionUpdated'] ?? false) ? 'checked' : '' }}
                            class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                        <span class="ml-2 text-sm text-gray-700">
                            <i class="fas fa-wifi mr-1 text-blue-600"></i>
                            Conexão Atualizada
                        </span>
                    </label>

                    <label class="flex items-center">
                        <input type="checkbox" name="events[messagesUpsert]" value="1"
                            {{ ($webhook['data']['events']['messagesUpsert'] ?? false) ? 'checked' : '' }}
                            class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                        <span class="ml-2 text-sm text-gray-700">
                            <i class="fas fa-inbox mr-1 text-yellow-600"></i>
                            Mensagens Recebidas
                        </span>
                    </label>

                    <label class="flex items-center">
                        <input type="checkbox" name="events[sendMessage]" value="1"
                            {{ ($webhook['data']['events']['sendMessage'] ?? false) ? 'checked' : '' }}
                            class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                        <span class="ml-2 text-sm text-gray-700">
                            <i class="fas fa-paper-plane mr-1 text-green-600"></i>
                            Mensagens Enviadas
                        </span>
                    </label>
                </div>
            </div>

            <div class="flex items-center justify-end">
                <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-6 rounded-lg transition">
                    <i class="fas fa-save mr-2"></i> Salvar Configurações
                </button>
            </div>
        </form>
    </div>

    {{-- Info Card --}}
    <div class="mt-6 bg-purple-50 border border-purple-200 rounded-xl p-6">
        <h3 class="text-purple-800 font-bold mb-2">
            <i class="fas fa-info-circle mr-2"></i> Sobre Webhooks
        </h3>
        <ul class="text-purple-700 text-sm space-y-2">
            <li><i class="fas fa-check mr-2"></i> O webhook receberá uma requisição POST para cada evento</li>
            <li><i class="fas fa-check mr-2"></i> O payload inclui: event, instance, data e timestamp</li>
            <li><i class="fas fa-check mr-2"></i> Headers incluem: x-webhook-event, x-instance-name, x-owner-jid</li>
            <li><i class="fas fa-check mr-2"></i> Use HTTPS em produção para segurança</li>
        </ul>
    </div>

    {{-- Current Config --}}
    @if(isset($webhook['data']))
        <div class="mt-6 bg-gray-50 border border-gray-200 rounded-xl p-6">
            <h3 class="text-gray-800 font-bold mb-2">
                <i class="fas fa-code mr-2"></i> Configuração Atual (JSON)
            </h3>
            <pre class="bg-gray-800 text-green-400 p-4 rounded-lg overflow-x-auto text-sm">{{ json_encode($webhook['data'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
        </div>
    @endif
</div>
@endsection
