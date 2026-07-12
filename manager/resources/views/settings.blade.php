@extends('layouts.app')

@section('title', 'Configurações - WhatsApp Manager')

@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-bold text-gray-800">
        <i class="fas fa-cog mr-2 text-gray-600"></i> Configurações
    </h1>
    <p class="text-gray-600 mt-1">Configurações gerais do WhatsApp Manager</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    {{-- API Settings --}}
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="bg-gray-800 px-6 py-4">
            <h2 class="text-white font-bold text-lg">
                <i class="fas fa-server mr-2"></i> API WhatsApp Go
            </h2>
        </div>

        <div class="p-6 space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">URL da API</label>
                <div class="flex items-center">
                    <input type="text" value="{{ config('services.whatsapp.api_url') }}" readonly
                        class="flex-1 px-4 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-600">
                    <a href="{{ config('services.whatsapp.api_url') }}/health" target="_blank" class="ml-2 bg-green-500 hover:bg-green-600 text-white py-2 px-4 rounded-lg transition">
                        <i class="fas fa-heartbeat"></i>
                    </a>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">API Key</label>
                <input type="password" value="{{ config('services.whatsapp.api_key') }}" readonly
                    class="w-full px-4 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-600">
            </div>
        </div>
    </div>

    {{-- App Info --}}
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="bg-green-600 px-6 py-4">
            <h2 class="text-white font-bold text-lg">
                <i class="fas fa-info-circle mr-2"></i> Sobre o Sistema
            </h2>
        </div>

        <div class="p-6 space-y-4">
            <div class="flex justify-between items-center py-2 border-b">
                <span class="text-gray-600">Versão</span>
                <span class="font-medium">1.0.0</span>
            </div>
            <div class="flex justify-between items-center py-2 border-b">
                <span class="text-gray-600">Laravel</span>
                <span class="font-medium">{{ app()->version() }}</span>
            </div>
            <div class="flex justify-between items-center py-2 border-b">
                <span class="text-gray-600">PHP</span>
                <span class="font-medium">{{ phpversion() }}</span>
            </div>
            <div class="flex justify-between items-center py-2">
                <span class="text-gray-600">API Backend</span>
                <span class="font-medium">whatsapp-api-go</span>
            </div>
        </div>
    </div>

    {{-- Endpoints --}}
    <div class="bg-white rounded-xl shadow-md overflow-hidden lg:col-span-2">
        <div class="bg-blue-600 px-6 py-4">
            <h2 class="text-white font-bold text-lg">
                <i class="fas fa-list mr-2"></i> Endpoints Disponíveis
            </h2>
        </div>

        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Método</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rota</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Descrição</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 text-sm">
                        <tr>
                            <td class="px-4 py-3"><span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs font-bold">GET</span></td>
                            <td class="px-4 py-3 font-mono text-xs">/instance/fetchInstances</td>
                            <td class="px-4 py-3">Listar instâncias</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-3"><span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs font-bold">POST</span></td>
                            <td class="px-4 py-3 font-mono text-xs">/instance/create</td>
                            <td class="px-4 py-3">Criar instância</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-3"><span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs font-bold">GET</span></td>
                            <td class="px-4 py-3 font-mono text-xs">/instance/connect/:name</td>
                            <td class="px-4 py-3">Conectar via QR Code</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-3"><span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs font-bold">GET</span></td>
                            <td class="px-4 py-3 font-mono text-xs">/instance/connectionState/:name</td>
                            <td class="px-4 py-3">Status da conexão</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-3"><span class="bg-red-100 text-red-800 px-2 py-1 rounded text-xs font-bold">DELETE</span></td>
                            <td class="px-4 py-3 font-mono text-xs">/instance/logout/:name</td>
                            <td class="px-4 py-3">Desconectar</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-3"><span class="bg-red-100 text-red-800 px-2 py-1 rounded text-xs font-bold">DELETE</span></td>
                            <td class="px-4 py-3 font-mono text-xs">/instance/delete/:name</td>
                            <td class="px-4 py-3">Remover instância</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-3"><span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs font-bold">POST</span></td>
                            <td class="px-4 py-3 font-mono text-xs">/message/sendText/:name</td>
                            <td class="px-4 py-3">Enviar texto</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-3"><span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs font-bold">POST</span></td>
                            <td class="px-4 py-3 font-mono text-xs">/message/sendLink/:name</td>
                            <td class="px-4 py-3">Enviar link</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-3"><span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs font-bold">POST</span></td>
                            <td class="px-4 py-3 font-mono text-xs">/message/sendMedia/:name</td>
                            <td class="px-4 py-3">Enviar mídia</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-3"><span class="bg-orange-100 text-orange-800 px-2 py-1 rounded text-xs font-bold">PUT</span></td>
                            <td class="px-4 py-3 font-mono text-xs">/webhook/set/:name</td>
                            <td class="px-4 py-3">Configurar webhook</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
