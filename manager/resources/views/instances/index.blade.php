@extends('layouts.app')

@section('title', 'Instâncias - WhatsApp Manager')

@section('content')
<div class="mb-8 flex justify-between items-center">
    <div>
        <h1 class="text-3xl font-bold text-gray-800 dark:text-white">
            <i class="fas fa-server mr-2 text-green-600"></i> Instâncias
        </h1>
        <p class="text-gray-600 dark:text-gray-400 mt-1">Gerencie suas instâncias WhatsApp</p>
    </div>
    <a href="{{ route('whatsapp.create') }}" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg transition">
        <i class="fas fa-plus mr-2"></i> Nova Instância
    </a>
</div>

@if($instances->count() > 0)
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($instances as $instance)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden card-hover transition">
                <div class="gradient-bg px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <i class="fab fa-whatsapp text-white text-2xl mr-3"></i>
                            <span class="text-white font-bold text-lg">{{ $instance->name }}</span>
                        </div>
                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $instance->status_badge }}">
                            {{ $instance->status }}
                        </span>
                    </div>
                </div>

                <div class="p-6">
                    <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">{{ $instance->description ?? 'Sem descrição' }}</p>

                    <div class="space-y-2 text-sm">
                        <div class="flex items-center text-gray-600 dark:text-gray-400">
                            <i class="fas fa-phone mr-2 w-4"></i>
                            <span>{{ $instance->phone ?? 'Não conectado' }}</span>
                        </div>
                        <div class="flex items-center text-gray-600 dark:text-gray-400">
                            <i class="fas fa-clock mr-2 w-4"></i>
                            <span>Criado: {{ $instance->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                    </div>

                    <div class="mt-6 flex flex-wrap gap-2">
                        <a href="{{ route('whatsapp.show', $instance->name) }}" class="bg-blue-500 hover:bg-blue-600 text-white text-sm py-1 px-3 rounded-lg transition">
                            <i class="fas fa-eye mr-1"></i> Detalhes
                        </a>
                        <a href="{{ route('messages.send', $instance->name) }}" class="bg-green-500 hover:bg-green-600 text-white text-sm py-1 px-3 rounded-lg transition">
                            <i class="fas fa-paper-plane mr-1"></i> Enviar
                        </a>
                        <a href="{{ route('webhook.settings', $instance->name) }}" class="bg-purple-500 hover:bg-purple-600 text-white text-sm py-1 px-3 rounded-lg transition">
                            <i class="fas fa-link mr-1"></i> Webhook
                        </a>
                    </div>

                    <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <form action="{{ route('whatsapp.delete', $instance->name) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja remover esta instância?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-500 hover:text-red-700 text-sm">
                                <i class="fas fa-trash mr-1"></i> Remover
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@else
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-12 text-center">
        <i class="fas fa-inbox text-6xl text-gray-300 dark:text-gray-600 mb-4"></i>
        <p class="text-gray-500 dark:text-gray-400 text-lg mb-4">Nenhuma instância encontrada</p>
        <a href="{{ route('whatsapp.create') }}" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg transition">
            <i class="fas fa-plus mr-2"></i> Criar Primeira Instância
        </a>
    </div>
@endif
@endsection
