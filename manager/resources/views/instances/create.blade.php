@extends('layouts.app')

@section('title', 'Criar Instância - WhatsApp Manager')

@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-bold text-gray-800">
        <i class="fas fa-plus-circle mr-2 text-green-600"></i> Criar Nova Instância
    </h1>
    <p class="text-gray-600 mt-1">Crie uma nova instância WhatsApp para conectar</p>
</div>

<div class="max-w-2xl">
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="gradient-bg px-6 py-4">
            <h2 class="text-white font-bold text-lg">
                <i class="fab fa-whatsapp mr-2"></i> Configurar Instância
            </h2>
        </div>

        <form action="{{ route('whatsapp.store') }}" method="POST" class="p-6">
            @csrf

            <div class="mb-6">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                    Nome da Instância <span class="text-red-500">*</span>
                </label>
                <input type="text" name="name" id="name" value="{{ old('name') }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent @error('name') border-red-500 @enderror"
                    placeholder="Ex: minha-instancia" required>
                @error('name')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-sm text-gray-500">Nome único para identificar a instância</p>
            </div>

            <div class="mb-6">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                    Descrição
                </label>
                <textarea name="description" id="description" rows="3"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                    placeholder="Descrição opcional da instância">{{ old('description') }}</textarea>
            </div>

            <div class="flex items-center justify-end space-x-4">
                <a href="{{ route('whatsapp.instances') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg transition">
                    <i class="fas fa-times mr-2"></i> Cancelar
                </a>
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg transition">
                    <i class="fas fa-plus mr-2"></i> Criar Instância
                </button>
            </div>
        </form>
    </div>

    {{-- Info Card --}}
    <div class="mt-6 bg-blue-50 border border-blue-200 rounded-xl p-6">
        <h3 class="text-blue-800 font-bold mb-2">
            <i class="fas fa-info-circle mr-2"></i> O que acontece depois?
        </h3>
        <ul class="text-blue-700 text-sm space-y-2">
            <li><i class="fas fa-check mr-2"></i> Uma instância será criada na API</li>
            <li><i class="fas fa-check mr-2"></i> Um token será gerado automaticamente</li>
            <li><i class="fas fa-check mr-2"></i> Você poderá conectar via QR Code ou código de pareamento</li>
            <li><i class="fas fa-check mr-2"></i> Após conectar, poderá enviar e receber mensagens</li>
        </ul>
    </div>
</div>
@endsection
