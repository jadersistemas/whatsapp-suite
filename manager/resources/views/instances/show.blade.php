@extends('layouts.app')

@section('title', $instance->name . ' - WhatsApp Manager')

@section('content')
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 dark:text-white">
                <i class="fab fa-whatsapp mr-2 text-green-600"></i> {{ $instance->name }}
            </h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">{{ $instance->description ?? 'Sem descrição' }}</p>
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('whatsapp.chat', $instance->name) }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition">
                <i class="fas fa-comments mr-2"></i> Chat
            </a>
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
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-xl font-bold text-gray-800 dark:text-white">
                <i class="fas fa-wifi mr-2 text-green-600"></i> Status da Conexão
            </h2>
        </div>

        <div class="p-6">
            <div class="text-center mb-6">
                <span class="px-4 py-2 text-lg font-bold rounded-full {{ $instance->status_badge }}">
                    {{ $instance->status }}
                </span>
                @if($instance->owner_jid)
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">{{ $instance->owner_jid }}</p>
                @endif
            </div>

            @if(in_array($instance->status, ['open', 'OPEN', 'ONLINE']))
                {{-- Connected State --}}
                <div class="text-center py-6">
                    <div class="inline-flex items-center justify-center w-20 h-20 bg-green-100 dark:bg-green-900 rounded-full mb-4">
                        <i class="fas fa-check-circle text-green-600 dark:text-green-400 text-4xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-green-700 dark:text-green-400 mb-2">Instância Conectada</h3>
                    <p class="text-gray-500 dark:text-gray-400 mb-2">WhatsApp conectado e pronto para uso</p>
                    @if($instance->phone)
                        <div class="mt-3 inline-flex items-center px-4 py-2 bg-green-100 dark:bg-green-900 rounded-full">
                            <i class="fas fa-phone mr-2 text-green-600 dark:text-green-400"></i>
                            <span class="font-medium text-green-800 dark:text-green-300">{{ $instance->phone }}</span>
                        </div>
                    @endif
                    @if($instance->owner_jid)
                        <p class="mt-2 text-xs text-gray-400 dark:text-gray-500">{{ $instance->owner_jid }}</p>
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
                        <p class="mt-2 text-gray-600 dark:text-gray-400">Gerando QR Code...</p>
                    </div>

                    <div id="qr-result" class="hidden mt-4">
                        <div class="qr-container inline-block">
                            <img id="qr-image" src="" alt="QR Code" class="max-w-xs">
                        </div>
                        <p id="qr-code-text" class="mt-2 text-lg font-mono text-center text-gray-700 dark:text-gray-300 break-all"></p>
                        <p class="mt-4 text-sm text-gray-500 dark:text-gray-400">Escaneie com o WhatsApp no seu celular</p>
                    </div>
                </div>

                {{-- Pairing Code Section --}}
                <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <h3 class="font-bold text-gray-800 dark:text-white mb-4">
                        <i class="fas fa-mobile-alt mr-2"></i> Conectar via Código de Pareamento
                    </h3>

                    <form onsubmit="connectPairing(event)">
                        <div class="flex gap-2">
                            <input type="text" id="pairing-phone" placeholder="5511999999999"
                                class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                required>
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition">
                                <i class="fas fa-link mr-2"></i> Conectar
                            </button>
                        </div>
                    </form>

                    <div id="pairing-loading" class="hidden mt-4 text-center">
                        <i class="fas fa-spinner fa-spin text-2xl text-blue-600"></i>
                        <p class="mt-2 text-gray-600 dark:text-gray-400">Gerando código de pareamento...</p>
                    </div>

                    <div id="pairing-result" class="hidden mt-4 text-center">
                        <p class="text-gray-600 dark:text-gray-400 mb-2">Código de pareamento:</p>
                        <p id="pairing-code" class="text-3xl font-mono font-bold text-blue-600 dark:text-blue-400"></p>
                    </div>
                </div>
            @endif

            {{-- Actions --}}
            <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700 space-y-2">
                <button onclick="refreshStatus()" class="w-full bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-800 dark:text-white font-bold py-2 px-4 rounded-lg transition">
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
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-xl font-bold text-gray-800 dark:text-white">
                    <i class="fas fa-info-circle mr-2 text-blue-600"></i> Detalhes
                </h2>
            </div>

            <div class="p-6 space-y-4">
                <div class="flex justify-between items-center py-2 border-b border-gray-200 dark:border-gray-700">
                    <span class="text-gray-600 dark:text-gray-400">Nome</span>
                    <span class="font-medium text-gray-800 dark:text-white">{{ $instance->name }}</span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-gray-200 dark:border-gray-700">
                    <span class="text-gray-600 dark:text-gray-400">Telefone</span>
                    <span class="font-medium text-gray-800 dark:text-white">{{ $instance->phone ?? '-' }}</span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-gray-200 dark:border-gray-700">
                    <span class="text-gray-600 dark:text-gray-400">JID</span>
                    <span class="font-medium text-sm text-gray-800 dark:text-white">{{ $instance->owner_jid ?? '-' }}</span>
                </div>
                <div class="py-2 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-gray-600 dark:text-gray-400">API Key (Token)</span>
                        <div class="flex space-x-1">
                            <button onclick="toggleApiKey()" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 p-1" title="Mostrar/Ocultar">
                                <i id="api-key-eye" class="fas fa-eye"></i>
                            </button>
                            <button onclick="copyApiKey()" class="text-gray-500 hover:text-green-600 dark:text-gray-400 dark:hover:text-green-400 p-1" title="Copiar">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                    </div>
                    <div class="flex items-center bg-gray-100 dark:bg-gray-700 rounded-lg px-3 py-2">
                        <code id="api-key-value" class="flex-1 text-sm font-mono text-gray-500 dark:text-gray-400 select-all">••••••••••••••••••••••••••••••••</code>
                        <span id="api-key-copied" class="hidden text-green-600 text-xs ml-2">Copiado!</span>
                    </div>
                    <input type="hidden" id="api-key-hidden" value="{{ $instance->token }}">
                </div>
                <div class="flex justify-between items-center py-2 border-b border-gray-200 dark:border-gray-700">
                    <span class="text-gray-600 dark:text-gray-400">Criado em</span>
                    <span class="font-medium text-gray-800 dark:text-white">{{ $instance->created_at->format('d/m/Y H:i') }}</span>
                </div>
                <div class="flex justify-between items-center py-2">
                    <span class="text-gray-600 dark:text-gray-400">Atualizado em</span>
                    <span class="font-medium text-gray-800 dark:text-white">{{ $instance->updated_at->format('d/m/Y H:i') }}</span>
                </div>
            </div>
        </div>

        {{-- Quick Send --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-xl font-bold text-gray-800 dark:text-white">
                    <i class="fas fa-paper-plane mr-2 text-green-600"></i> Envio Rápido
                </h2>
            </div>

            <div class="p-6">
                <form action="{{ route('messages.text', $instance->name) }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Número</label>
                        <input type="text" name="number" placeholder="5511999999999"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                            required>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Mensagem</label>
                        <textarea name="text" rows="3" placeholder="Digite sua mensagem..."
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                            required></textarea>
                    </div>
                    <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg transition">
                        <i class="fas fa-paper-plane mr-2"></i> Enviar
                    </button>
                </form>
            </div>
        </div>

        {{-- Instance Settings --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-xl font-bold text-gray-800 dark:text-white">
                    <i class="fas fa-cog mr-2 text-indigo-600"></i> Configurações
                </h2>
            </div>

            <div class="p-6">
                <form id="settings-form" onsubmit="saveSettings(event)">
                    @csrf
                    <div class="space-y-4">
                        <div class="p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <label class="flex items-center justify-between cursor-pointer">
                                <div class="flex items-center">
                                    <i class="fas fa-phone-slash mr-3 text-red-500"></i>
                                    <div>
                                        <span class="font-medium text-gray-800 dark:text-white">Rejeitar Chamadas</span>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Rejeita todas as chamadas recebidas</p>
                                    </div>
                                </div>
                                <input type="checkbox" name="rejectCalls" value="1" class="w-5 h-5 text-red-600 rounded focus:ring-red-500"
                                    {{ ($instance->settings['rejectCalls'] ?? false) ? 'checked' : '' }}
                                    onchange="toggleRejectMessage()">
                            </label>
                            <div id="reject-message-container" class="mt-3 {{ ($instance->settings['rejectCalls'] ?? false) ? '' : 'hidden' }}">
                                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Mensagem automática:</label>
                                <input type="text" name="rejectCallMessage" value="{{ $instance->settings['rejectCallMessage'] ?? 'Esse número não recebe ligações, por favor envie um texto ou áudio!' }}"
                                    class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-red-500"
                                    placeholder="Mensagem ao rejeitar chamada">
                            </div>
                        </div>

                        <label class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition cursor-pointer">
                            <div class="flex items-center">
                                <i class="fas fa-users-slash mr-3 text-orange-500"></i>
                                <div>
                                    <span class="font-medium text-gray-800 dark:text-white">Ignorar Grupos</span>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Ignora mensagens de grupos</p>
                                </div>
                            </div>
                            <input type="checkbox" name="ignoreGroups" value="1" class="w-5 h-5 text-orange-600 rounded focus:ring-orange-500"
                                {{ ($instance->settings['ignoreGroups'] ?? false) ? 'checked' : '' }}>
                        </label>

                        <label class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition cursor-pointer">
                            <div class="flex items-center">
                                <i class="fas fa-circle mr-3 text-green-500"></i>
                                <div>
                                    <span class="font-medium text-gray-800 dark:text-white">Sempre Online</span>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Permanece sempre online</p>
                                </div>
                            </div>
                            <input type="checkbox" name="alwaysOnline" value="1" class="w-5 h-5 text-green-600 rounded focus:ring-green-500"
                                {{ ($instance->settings['alwaysOnline'] ?? false) ? 'checked' : '' }}>
                        </label>

                        <label class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition cursor-pointer">
                            <div class="flex items-center">
                                <i class="fas fa-check-double mr-3 text-blue-500"></i>
                                <div>
                                    <span class="font-medium text-gray-800 dark:text-white">Visualizar Mensagens</span>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Marca todas as mensagens como lidas</p>
                                </div>
                            </div>
                            <input type="checkbox" name="readMessages" value="1" class="w-5 h-5 text-blue-600 rounded focus:ring-blue-500"
                                {{ ($instance->settings['readMessages'] ?? false) ? 'checked' : '' }}>
                        </label>

                        <label class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition cursor-pointer">
                            <div class="flex items-center">
                                <i class="fas fa-history mr-3 text-purple-500"></i>
                                <div>
                                    <span class="font-medium text-gray-800 dark:text-white">Sincronizar Histórico</span>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Sincroniza histórico completo ao conectar</p>
                                </div>
                            </div>
                            <input type="checkbox" name="syncFullHistory" value="1" class="w-5 h-5 text-purple-600 rounded focus:ring-purple-500"
                                {{ ($instance->settings['syncFullHistory'] ?? false) ? 'checked' : '' }}>
                        </label>

                        <label class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition cursor-pointer">
                            <div class="flex items-center">
                                <i class="fas fa-eye mr-3 text-teal-500"></i>
                                <div>
                                    <span class="font-medium text-gray-800 dark:text-white">Visualizar Status</span>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Marca todos os status como visualizados</p>
                                </div>
                            </div>
                            <input type="checkbox" name="viewStatus" value="1" class="w-5 h-5 text-teal-600 rounded focus:ring-teal-500"
                                {{ ($instance->settings['viewStatus'] ?? false) ? 'checked' : '' }}>
                        </label>

                        <div class="p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <label class="flex items-center justify-between cursor-pointer">
                                <div class="flex items-center">
                                    <i class="fas fa-reply mr-3 text-cyan-500"></i>
                                    <div>
                                        <span class="font-medium text-gray-800 dark:text-white">Resposta de Ausência</span>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Envia resposta automática quando ausente</p>
                                    </div>
                                </div>
                                <input type="checkbox" name="autoReply" value="1" class="w-5 h-5 text-cyan-600 rounded focus:ring-cyan-500"
                                    {{ ($instance->settings['autoReply'] ?? false) ? 'checked' : '' }}
                                    onchange="toggleAutoReplyMessage()">
                            </label>
                            <div id="auto-reply-message-container" class="mt-3 {{ ($instance->settings['autoReply'] ?? false) ? '' : 'hidden' }}">
                                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Mensagem de ausência:</label>
                                <input type="text" name="autoReplyMessage" value="{{ $instance->settings['autoReplyMessage'] ?? 'Olá! No momento não posso atender, mas deixe sua mensagem que retorno em breve!' }}"
                                    class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-cyan-500"
                                    placeholder="Mensagem de ausência">
                            </div>
                        </div>

                        {{-- Chatbot --}}
                        <div class="p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <label class="flex items-center justify-between cursor-pointer">
                                <div class="flex items-center">
                                    <i class="fas fa-robot mr-3 text-violet-500"></i>
                                    <div>
                                        <span class="font-medium text-gray-800 dark:text-white">Chatbot</span>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Respostas automáticas com menus e camadas</p>
                                    </div>
                                </div>
                                <input type="checkbox" name="chatbotEnabled" value="1" class="w-5 h-5 text-violet-600 rounded focus:ring-violet-500"
                                    {{ ($instance->settings['chatbotEnabled'] ?? false) ? 'checked' : '' }}
                                    onchange="toggleChatbot()">
                            </label>

                            <div id="chatbot-container" class="mt-3 {{ ($instance->settings['chatbotEnabled'] ?? false) ? '' : 'hidden' }}">
                                <div class="flex justify-between items-center mb-2">
                                    <label class="text-xs text-gray-500 dark:text-gray-400">Fluxos (camadas):</label>
                                    <button type="button" onclick="addChatbotFlow()" class="text-xs text-violet-600 hover:text-violet-700">
                                        <i class="fas fa-plus mr-1"></i> Adicionar Fluxo
                                    </button>
                                </div>
                                <div id="chatbot-flows" class="space-y-3">
                                    @if(!empty($instance->settings['chatbotFlows']))
                                        @foreach($instance->settings['chatbotFlows'] as $flowIndex => $flow)
                                            <div class="chatbot-flow bg-white dark:bg-gray-800 p-3 rounded-lg border border-gray-200 dark:border-gray-600">
                                                <div class="flex justify-between items-center mb-2">
                                                    <span class="text-xs font-bold text-violet-600 dark:text-violet-400">Fluxo {{ $flowIndex + 1 }}</span>
                                                    <button type="button" onclick="removeChatbotFlow(this)" class="text-red-500 hover:text-red-700 text-xs">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                                <div class="flex gap-2 mb-2">
                                                    <input type="text" name="chatbotFlows[{{ $flowIndex }}][trigger]" value="{{ $flow['trigger'] ?? '' }}"
                                                        class="flex-1 px-2 py-1 text-xs border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                                        placeholder="Gatilho (ex: menu, ajuda, 1)">
                                                    <select name="chatbotFlows[{{ $flowIndex }}][matchType]"
                                                        class="px-2 py-1 text-xs border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                                        <option value="partial" {{ ($flow['matchType'] ?? 'partial') === 'partial' ? 'selected' : '' }}>Parcial</option>
                                                        <option value="exact" {{ ($flow['matchType'] ?? '') === 'exact' ? 'selected' : '' }}>Exata</option>
                                                    </select>
                                                </div>
                                                <textarea name="chatbotFlows[{{ $flowIndex }}][message]" rows="2"
                                                    class="w-full px-2 py-1 text-xs border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white mb-2"
                                                    placeholder="Mensagem de resposta">{{ $flow['message'] ?? '' }}</textarea>
                                                <div class="flex justify-between items-center mb-1">
                                                    <span class="text-xs text-gray-500 dark:text-gray-400">Opções:</span>
                                                    <button type="button" onclick="addChatbotOption(this)" class="text-xs text-violet-600 hover:text-violet-700">
                                                        <i class="fas fa-plus"></i>
                                                    </button>
                                                </div>
                                                <div class="chatbot-options space-y-1">
                                                    @if(!empty($flow['options']))
                                                        @foreach($flow['options'] as $optIndex => $opt)
                                                            <div class="flex gap-1 items-center chatbot-option">
                                                                <input type="text" name="chatbotFlows[{{ $flowIndex }}][options][{{ $optIndex }}][id]" value="{{ $opt['id'] ?? '' }}"
                                                                    class="w-16 px-1 py-0.5 text-xs border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                                                    placeholder="ID">
                                                                <input type="text" name="chatbotFlows[{{ $flowIndex }}][options][{{ $optIndex }}][text]" value="{{ $opt['text'] ?? '' }}"
                                                                    class="flex-1 px-1 py-0.5 text-xs border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                                                    placeholder="Texto">
                                                                <input type="text" name="chatbotFlows[{{ $flowIndex }}][options][{{ $optIndex }}][next]" value="{{ $opt['next'] ?? '' }}"
                                                                    class="w-20 px-1 py-0.5 text-xs border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                                                    placeholder="Próximo ID">
                                                                <button type="button" onclick="removeChatbotOption(this)" class="text-red-500 hover:text-red-700 text-xs">
                                                                    <i class="fas fa-times"></i>
                                                                </button>
                                                            </div>
                                                        @endforeach
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="settings-result" class="hidden mt-4"></div>

                    <button type="submit" class="w-full mt-4 bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg transition">
                        <i class="fas fa-save mr-2"></i> Salvar Configurações
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
                        <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">O QR anterior pode ter expirado. Use o código de pareamento abaixo ou aguarde e tente novamente.</p>
                    </div>`;
            } else {
                alert('Resposta inesperada: ' + JSON.stringify(data));
            }
        } catch (error) {
            alert('Erro ao gerar QR Code: ' + error.message);
        } finally {
            setTimeout(() => {
                document.getElementById('qr-loading').classList.add('hidden');
                document.getElementById('qr-loading').innerHTML = '<i class="fas fa-spinner fa-spin text-4xl text-green-600"></i><p class="mt-2 text-gray-600 dark:text-gray-400">Gerando QR Code...</p>';
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

    // Auto-refresh disabled to prevent losing form data

    function toggleRejectMessage() {
        const checkbox = document.querySelector('input[name="rejectCalls"]');
        const container = document.getElementById('reject-message-container');
        if (checkbox.checked) {
            container.classList.remove('hidden');
        } else {
            container.classList.add('hidden');
        }
    }

    function toggleAutoReplyMessage() {
        const checkbox = document.querySelector('input[name="autoReply"]');
        const container = document.getElementById('auto-reply-message-container');
        if (checkbox.checked) {
            container.classList.remove('hidden');
        } else {
            container.classList.add('hidden');
        }
    }

    let apiKeyVisible = false;

    function toggleApiKey() {
        const value = document.getElementById('api-key-value');
        const hidden = document.getElementById('api-key-hidden');
        const eye = document.getElementById('api-key-eye');

        apiKeyVisible = !apiKeyVisible;

        if (apiKeyVisible) {
            value.textContent = hidden.value;
            eye.className = 'fas fa-eye-slash';
        } else {
            value.textContent = '••••••••••••••••••••••••••••••••';
            eye.className = 'fas fa-eye';
        }
    }

    function copyApiKey() {
        const hidden = document.getElementById('api-key-hidden');
        const copied = document.getElementById('api-key-copied');

        navigator.clipboard.writeText(hidden.value).then(() => {
            copied.classList.remove('hidden');
            setTimeout(() => copied.classList.add('hidden'), 2000);
        });
    }

    function toggleChatbot() {
        const checkbox = document.querySelector('input[name="chatbotEnabled"]');
        const container = document.getElementById('chatbot-container');
        if (checkbox.checked) {
            container.classList.remove('hidden');
        } else {
            container.classList.add('hidden');
        }
    }

    let flowCounter = document.querySelectorAll('.chatbot-flow').length;

    function addChatbotFlow() {
        const container = document.getElementById('chatbot-flows');
        const flowHtml = `
            <div class="chatbot-flow bg-white dark:bg-gray-800 p-3 rounded-lg border border-gray-200 dark:border-gray-600">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-xs font-bold text-violet-600 dark:text-violet-400">Fluxo ${flowCounter + 1}</span>
                    <button type="button" onclick="removeChatbotFlow(this)" class="text-red-500 hover:text-red-700 text-xs">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
                <div class="flex gap-2 mb-2">
                    <input type="text" name="chatbotFlows[${flowCounter}][trigger]" class="flex-1 px-2 py-1 text-xs border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white" placeholder="Gatilho (ex: menu, ajuda, 1)">
                    <select name="chatbotFlows[${flowCounter}][matchType]" class="px-2 py-1 text-xs border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        <option value="partial">Parcial</option>
                        <option value="exact">Exata</option>
                    </select>
                </div>
                <textarea name="chatbotFlows[${flowCounter}][message]" rows="2" class="w-full px-2 py-1 text-xs border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white mb-2" placeholder="Mensagem de resposta"></textarea>
                <div class="flex justify-between items-center mb-1">
                    <span class="text-xs text-gray-500 dark:text-gray-400">Opções:</span>
                    <button type="button" onclick="addChatbotOption(this)" class="text-xs text-violet-600 hover:text-violet-700"><i class="fas fa-plus"></i></button>
                </div>
                <div class="chatbot-options space-y-1"></div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', flowHtml);
        flowCounter++;
    }

    function removeChatbotFlow(btn) {
        btn.closest('.chatbot-flow').remove();
    }

    function addChatbotOption(btn) {
        const flow = btn.closest('.chatbot-flow');
        const container = flow.querySelector('.chatbot-options');
        const flowIndex = Array.from(document.querySelectorAll('.chatbot-flow')).indexOf(flow);
        const optIndex = container.querySelectorAll('.chatbot-option').length;

        const optHtml = `
            <div class="flex gap-1 items-center chatbot-option">
                <input type="text" name="chatbotFlows[${flowIndex}][options][${optIndex}][id]" class="w-16 px-1 py-0.5 text-xs border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white" placeholder="ID">
                <input type="text" name="chatbotFlows[${flowIndex}][options][${optIndex}][text]" class="flex-1 px-1 py-0.5 text-xs border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white" placeholder="Texto">
                <input type="text" name="chatbotFlows[${flowIndex}][options][${optIndex}][next]" class="w-20 px-1 py-0.5 text-xs border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white" placeholder="Próximo ID">
                <button type="button" onclick="removeChatbotOption(this)" class="text-red-500 hover:text-red-700 text-xs"><i class="fas fa-times"></i></button>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', optHtml);
    }

    function removeChatbotOption(btn) {
        btn.closest('.chatbot-option').remove();
    }

    async function saveSettings(e) {
        e.preventDefault();
        const form = document.getElementById('settings-form');
        const result = document.getElementById('settings-result');
        const formData = new FormData(form);
        const settings = {};

        // Get all checkbox values
        ['rejectCalls', 'ignoreGroups', 'alwaysOnline', 'readMessages', 'syncFullHistory', 'viewStatus', 'autoReply'].forEach(key => {
            settings[key] = formData.has(key);
        });

        // Get reject call message
        settings.rejectCallMessage = formData.get('rejectCallMessage') || 'Esse número não recebe ligações, por favor envie um texto ou áudio!';

        // Get auto reply message
        settings.autoReplyMessage = formData.get('autoReplyMessage') || 'Olá! No momento não posso atender, mas deixe sua mensagem que retorno em breve!';

        // Get chatbot config
        const chatbotEnabled = formData.has('chatbotEnabled');
        const flows = [];
        document.querySelectorAll('.chatbot-flow').forEach((flow, flowIdx) => {
            const trigger = flow.querySelector('input[name*="[trigger]"]').value;
            const matchType = flow.querySelector('select[name*="[matchType]"]').value;
            const message = flow.querySelector('textarea[name*="[message]"]').value;
            const options = [];
            flow.querySelectorAll('.chatbot-option').forEach((opt) => {
                const inputs = opt.querySelectorAll('input');
                options.push({
                    id: inputs[0].value,
                    text: inputs[1].value,
                    next: inputs[2].value,
                });
            });
            flows.push({ id: `flow_${flowIdx}`, trigger, matchType, message, options });
        });
        settings.chatbot = { enabled: chatbotEnabled, flows };

        result.className = 'mt-4 p-3 rounded-lg bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300';
        result.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Salvando...';
        result.classList.remove('hidden');

        try {
            const csrfToken = form.querySelector('input[name="_token"]').value;
            const response = await fetch(`/instances/${instanceName}/settings`, {
                method: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                body: JSON.stringify(settings),
            });

            const data = await response.json();

            if (response.ok) {
                result.className = 'mt-4 p-3 rounded-lg bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300';
                result.innerHTML = '<i class="fas fa-check-circle mr-2"></i> Configurações salvas com sucesso!';
            } else {
                result.className = 'mt-4 p-3 rounded-lg bg-red-100 dark:bg-red-900 text-red-700 dark:text-red-300';
                result.innerHTML = '<i class="fas fa-times-circle mr-2"></i> Erro: ' + (data.error || 'Erro desconhecido');
            }
        } catch (error) {
            result.className = 'mt-4 p-3 rounded-lg bg-red-100 dark:bg-red-900 text-red-700 dark:text-red-300';
            result.innerHTML = '<i class="fas fa-times-circle mr-2"></i> Erro de conexão: ' + error.message;
        }

        setTimeout(() => result.classList.add('hidden'), 5000);
    }
</script>
@endpush
