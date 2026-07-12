@extends('layouts.app')

@section('title', 'Dashboard - WhatsApp Manager')

@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-bold text-gray-800">
        <i class="fas fa-tachometer-alt mr-2 text-green-600"></i> Dashboard
    </h1>
    <p class="text-gray-600 mt-1">Visão geral das instâncias WhatsApp</p>
</div>

{{-- Stats Cards --}}
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-xl shadow-md p-6 card-hover transition">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                <i class="fas fa-server text-2xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm text-gray-500">Total</p>
                <p class="text-2xl font-bold text-gray-800">{{ $stats['total'] }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-md p-6 card-hover transition">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-green-100 text-green-600">
                <i class="fas fa-check-circle text-2xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm text-gray-500">Online</p>
                <p class="text-2xl font-bold text-green-600">{{ $stats['online'] }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-md p-6 card-hover transition">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-red-100 text-red-600">
                <i class="fas fa-times-circle text-2xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm text-gray-500">Offline</p>
                <p class="text-2xl font-bold text-red-600">{{ $stats['offline'] }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-md p-6 card-hover transition">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                <i class="fas fa-spinner text-2xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm text-gray-500">Conectando</p>
                <p class="text-2xl font-bold text-yellow-600">{{ $stats['connecting'] }}</p>
            </div>
        </div>
    </div>
</div>

{{-- Quick Actions --}}
<div class="bg-white rounded-xl shadow-md p-6 mb-8">
    <h2 class="text-xl font-bold text-gray-800 mb-4">
        <i class="fas fa-bolt mr-2 text-yellow-500"></i> Ações Rápidas
    </h2>
    <div class="flex flex-wrap gap-4">
        <a href="{{ route('whatsapp.create') }}" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg transition">
            <i class="fas fa-plus mr-2"></i> Nova Instância
        </a>
        <a href="{{ route('whatsapp.instances') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition">
            <i class="fas fa-list mr-2"></i> Ver Instâncias
        </a>
    </div>
</div>

{{-- Instances List --}}
<div class="bg-white rounded-xl shadow-md overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200">
        <h2 class="text-xl font-bold text-gray-800">
            <i class="fas fa-server mr-2 text-green-600"></i> Instâncias Recentes
        </h2>
    </div>

    @if($instances->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nome</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Telefone</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Criado em</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($instances as $instance)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-full bg-green-100 flex items-center justify-center">
                                            <i class="fab fa-whatsapp text-green-600"></i>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $instance->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $instance->description ?? 'Sem descrição' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $instance->status_badge }}">
                                    {{ $instance->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $instance->phone ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $instance->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('whatsapp.show', $instance->name) }}" class="text-blue-600 hover:text-blue-900 mr-3">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('messages.send', $instance->name) }}" class="text-green-600 hover:text-green-900 mr-3">
                                    <i class="fas fa-paper-plane"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="px-6 py-12 text-center">
            <i class="fas fa-inbox text-6xl text-gray-300 mb-4"></i>
            <p class="text-gray-500 text-lg">Nenhuma instância encontrada</p>
            <a href="{{ route('whatsapp.create') }}" class="mt-4 inline-block bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg transition">
                <i class="fas fa-plus mr-2"></i> Criar Primeira Instância
            </a>
        </div>
    @endif
</div>
@endsection
