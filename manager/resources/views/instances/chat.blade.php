@extends('layouts.app')

@section('title', 'Chat - ' . $instance->name)

@section('content')
<div class="mb-4">
    <div class="flex items-center justify-between">
        <div class="flex items-center">
            <a href="{{ route('whatsapp.show', $instance->name) }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 mr-3">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-white">
                    <i class="fas fa-comments mr-2 text-green-600"></i> Chat
                </h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $instance->name }}</p>
            </div>
        </div>
        <div class="flex items-center space-x-2">
            <select id="chat-contact" onchange="loadChat()" class="px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                <option value="">Todas as conversas</option>
            </select>
            <button onclick="loadChat()" class="bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded-lg text-sm transition">
                <i class="fas fa-sync mr-1"></i> Atualizar
            </button>
        </div>
    </div>
</div>

<div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden" style="height: calc(100vh - 200px);">
    {{-- Chat Messages --}}
    <div id="chat-messages" class="h-full overflow-y-auto p-4 space-y-3">
        <div class="text-center text-gray-500 dark:text-gray-400 py-8">
            <i class="fas fa-comments text-4xl mb-2"></i>
            <p>Selecione uma conversa ou clique em Atualizar</p>
        </div>
    </div>

    {{-- Chat Input --}}
    <div class="border-t border-gray-200 dark:border-gray-700 p-4">
        <div class="flex gap-2">
            <input type="text" id="chat-number" placeholder="Número (5511999999999)"
                class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-green-500">
            <input type="text" id="chat-input" placeholder="Digite sua mensagem..."
                class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-green-500"
                onkeypress="if(event.key==='Enter')sendMessage()">
            <button onclick="sendMessage()" class="bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded-lg text-sm transition">
                <i class="fas fa-paper-plane"></i>
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const instanceName = '{{ $instance->name }}';
    let currentChat = '';

    async function loadChat() {
        const chatJid = document.getElementById('chat-contact').value;
        const container = document.getElementById('chat-messages');

        container.innerHTML = '<div class="text-center py-4"><i class="fas fa-spinner fa-spin text-green-600"></i></div>';

        try {
            const url = chatJid
                ? `/instances/${instanceName}/messages?chatJid=${chatJid}&limit=100`
                : `/instances/${instanceName}/messages?limit=100`;
            const response = await fetch(url);
            const data = await response.json();

            if (data.messages && data.messages.records) {
                renderMessages(data.messages.records);
                updateContactList(data.messages.records);
            }
        } catch (error) {
            container.innerHTML = '<div class="text-center text-red-500 py-4">Erro ao carregar mensagens</div>';
        }
    }

    function renderMessages(messages) {
        const container = document.getElementById('chat-messages');

        if (!messages || messages.length === 0) {
            container.innerHTML = '<div class="text-center text-gray-500 dark:text-gray-400 py-8"><i class="fas fa-inbox text-4xl mb-2"></i><p>Nenhuma mensagem encontrada</p></div>';
            return;
        }

        // Group by chat JID
        const grouped = {};
        messages.reverse().forEach(msg => {
            const jid = msg.keyRemoteJid || 'unknown';
            if (!grouped[jid]) grouped[jid] = [];
            grouped[jid].push(msg);
        });

        let html = '';
        for (const [jid, msgs] of Object.entries(grouped)) {
            const displayName = jid.replace('@s.whatsapp.net', '').replace('@g.us', ' (grupo)');
            html += `<div class="border-b border-gray-200 dark:border-gray-700 pb-2 mb-4">
                <div class="text-xs font-bold text-gray-500 dark:text-gray-400 mb-2 cursor-pointer hover:text-green-600" onclick="filterChat('${jid}')">
                    <i class="fas fa-user mr-1"></i> ${displayName}
                </div>`;

            msgs.forEach(msg => {
                const isMe = msg.keyFromMe;
                const content = parseContent(msg);
                const time = msg.messageTimestamp ? new Date(msg.messageTimestamp * 1000).toLocaleTimeString('pt-BR', {hour: '2-digit', minute: '2-digit'}) : '';
                const align = isMe ? 'justify-end' : 'justify-start';
                const bubble = isMe
                    ? 'bg-green-600 text-white rounded-l-xl rounded-tr-xl'
                    : 'bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-white rounded-r-xl rounded-tl-xl';

                html += `<div class="flex ${align}">
                    <div class="${bubble} px-4 py-2 max-w-xs lg:max-w-md">
                        <p class="text-sm whitespace-pre-wrap">${escapeHtml(content)}</p>
                        <p class="text-xs ${isMe ? 'text-green-200' : 'text-gray-500 dark:text-gray-400'} text-right mt-1">${time}</p>
                    </div>
                </div>`;
            });

            html += '</div>';
        }

        container.innerHTML = html;
        container.scrollTop = container.scrollHeight;
    }

    function parseContent(msg) {
        if (!msg.content) return '[mensagem]';

        const content = typeof msg.content === 'string' ? JSON.parse(msg.content) : msg.content;

        if (content.text) return content.text;
        if (content.caption) return `📎 ${content.caption}`;
        if (content.conversation) return content.conversation;
        if (content.extendedTextMessage && content.extendedTextMessage.text) return content.extendedTextMessage.text;

        return `[${msg.messageType || 'mensagem'}]`;
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function updateContactList(messages) {
        const contacts = new Set();
        messages.forEach(msg => {
            if (msg.keyRemoteJid) {
                contacts.add(msg.keyRemoteJid);
            }
        });

        const select = document.getElementById('chat-contact');
        const current = select.value;
        select.innerHTML = '<option value="">Todas as conversas</option>';

        contacts.forEach(jid => {
            const display = jid.replace('@s.whatsapp.net', '').replace('@g.us', ' (grupo)');
            const opt = document.createElement('option');
            opt.value = jid;
            opt.textContent = display;
            if (jid === current) opt.selected = true;
            select.appendChild(opt);
        });
    }

    function filterChat(jid) {
        document.getElementById('chat-contact').value = jid;
        loadChat();
    }

    async function sendMessage() {
        const number = document.getElementById('chat-number').value.trim();
        const text = document.getElementById('chat-input').value.trim();

        if (!number || !text) {
            alert('Preencha o número e a mensagem');
            return;
        }

        try {
            const csrfToken = '{{ csrf_token() }}';
            const response = await fetch(`/messages/${instanceName}/text`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ number, text }),
            });

            if (response.ok) {
                document.getElementById('chat-input').value = '';
                setTimeout(loadChat, 1000);
            } else {
                alert('Erro ao enviar mensagem');
            }
        } catch (error) {
            alert('Erro de conexão: ' + error.message);
        }
    }

    // Auto-refresh every 10 seconds
    setInterval(loadChat, 10000);
</script>
@endpush
