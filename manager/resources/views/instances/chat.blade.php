@extends('layouts.app')

@section('title', 'Chat - ' . $instance->name)

@section('content')
<style>
    .chat-container { display: flex; height: calc(100vh - 140px); }
    .chat-sidebar { width: 320px; border-right: 1px solid #e5e7eb; overflow-y: auto; }
    .dark .chat-sidebar { border-right-color: #374151; }
    .chat-main { flex: 1; display: flex; flex-direction: column; }
    .chat-header { padding: 12px 16px; border-bottom: 1px solid #e5e7eb; background: white; }
    .dark .chat-header { background: #1f2937; border-bottom-color: #374151; }
    .chat-messages { flex: 1; overflow-y: auto; padding: 16px; background: #e5ddd5; background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23d1d5db' fill-opacity='0.15'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E"); }
    .dark .chat-messages { background: #111827; }
    .chat-input-area { padding: 12px 16px; border-top: 1px solid #e5e7eb; background: white; }
    .dark .chat-input-area { background: #1f2937; border-top-color: #374151; }
    .contact-item { padding: 12px 16px; cursor: pointer; border-bottom: 1px solid #f3f4f6; }
    .dark .contact-item { border-bottom-color: #374151; }
    .contact-item:hover, .contact-item.active { background: #f3f4f6; }
    .dark .contact-item:hover, .dark .contact-item.active { background: #374151; }
    .msg-bubble { max-width: 65%; padding: 8px 12px; border-radius: 8px; position: relative; word-wrap: break-word; }
    .msg-sent { background: #dcf8c6; margin-left: auto; border-top-right-radius: 0; }
    .msg-received { background: white; margin-right: auto; border-top-left-radius: 0; }
    .dark .msg-sent { background: #005c4b; }
    .dark .msg-received { background: #1f2937; }
    .msg-time { font-size: 11px; color: #666; text-align: right; margin-top: 4px; }
    .dark .msg-time { color: #9ca3af; }
    .msg-check { color: #53bdeb; margin-left: 4px; }
    .unread-badge { background: #25d366; color: white; font-size: 11px; padding: 2px 6px; border-radius: 10px; }
    .typing-indicator { display: flex; gap: 4px; padding: 8px 12px; }
    .typing-dot { width: 8px; height: 8px; background: #999; border-radius: 50%; animation: typing 1.4s infinite; }
    .typing-dot:nth-child(2) { animation-delay: 0.2s; }
    .typing-dot:nth-child(3) { animation-delay: 0.4s; }
    @keyframes typing { 0%, 60%, 100% { transform: translateY(0); } 30% { transform: translateY(-4px); } }
    .online-dot { width: 10px; height: 10px; background: #25d366; border-radius: 50%; display: inline-block; }
</style>

<div class="chat-container bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden">
    {{-- Sidebar - Contacts --}}
    <div class="chat-sidebar bg-white dark:bg-gray-800">
        <div class="p-4 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between mb-3">
                <h2 class="font-bold text-gray-800 dark:text-white">Conversas</h2>
                <span class="text-xs text-gray-500 dark:text-gray-400" id="online-status">
                    <span class="online-dot"></span> Conectado
                </span>
            </div>
            <input type="text" id="search-contacts" placeholder="Buscar conversa..."
                class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white" oninput="filterContacts()">
        </div>
        <div id="contacts-list" class="overflow-y-auto" style="height: calc(100% - 120px);">
        </div>
    </div>

    {{-- Main Chat --}}
    <div class="chat-main">
        <div class="chat-header" id="chat-header">
            <div class="flex items-center">
                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-green-400 to-green-600 flex items-center justify-center mr-3" id="chat-avatar">
                    <i class="fas fa-user text-white"></i>
                </div>
                <div>
                    <p class="font-bold text-gray-800 dark:text-white" id="chat-name">Selecione uma conversa</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400" id="chat-status"></p>
                </div>
            </div>
            <div class="flex items-center space-x-3">
                <button onclick="loadChat()" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200" title="Atualizar">
                    <i class="fas fa-sync"></i>
                </button>
            </div>
        </div>

        <div class="chat-messages" id="chat-messages">
            <div class="flex items-center justify-center h-full text-gray-500 dark:text-gray-400">
                <div class="text-center">
                    <i class="fab fa-whatsapp text-6xl mb-4 text-green-500"></i>
                    <p class="text-lg">WhatsApp Web</p>
                    <p class="text-sm">Selecione uma conversa ao lado</p>
                </div>
            </div>
        </div>

        <div class="chat-input-area" id="chat-input-area" style="display: none;">
            <div class="flex items-center gap-2">
                <button class="text-gray-500 hover:text-gray-700 dark:text-gray-400">
                    <i class="fas fa-smile text-xl"></i>
                </button>
                <input type="text" id="chat-input" placeholder="Digite uma mensagem"
                    class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-green-500"
                    onkeypress="if(event.key==='Enter')sendMessage()">
                <button onclick="sendMessage()" class="bg-green-600 hover:bg-green-700 text-white p-2 rounded-full transition">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Hidden input for number --}}
<input type="hidden" id="chat-number" value="">
@endsection

@push('scripts')
<script>
    const instanceName = '{{ $instance->name }}';
    let currentChat = '';
    let allContacts = {};
    let lastMessageId = 0;
    let profilePics = {};

    // Poll for new messages every 2 seconds
    function startPolling() {
        setInterval(function() {
            if (currentChat) {
                loadChat(false);
            }
        }, 2000);
    }

    function handleNewMessage(msg) {
        const jid = msg.keyRemoteJid;
        if (!jid) return;

        const displayJid = normalizeJid(jid);

        // Update contact
        if (!allContacts[displayJid]) {
            allContacts[displayJid] = { displayJid, name: formatJidName(displayJid), lastMessage: '', time: '', unread: 0 };
        }
        allContacts[displayJid].lastMessage = parseContent(msg);
        allContacts[displayJid].time = formatTime(msg.messageTimestamp);
        if (!msg.keyFromMe) allContacts[displayJid].unread++;

        renderContacts();

        // If viewing this chat, add message
        if (displayJid === currentChat) {
            appendMessage(msg);
            allContacts[displayJid].unread = 0;
            renderContacts();
        }
    }

    function normalizeJid(jid) {
        // Remove device suffix (:XX) from JID before @
        return jid.replace(/:\d+(?=@)/, '');
    }

    function formatJidName(jid) {
        var number = jid.replace('@s.whatsapp.net', '').replace('@g.us', '');
        if (jid.includes('@g.us')) return 'Grupo ' + number;
        if (jid === 'status@broadcast') return 'Status';
        return number;
    }

    function getInitial(name) {
        return name.charAt(0).toUpperCase();
    }

    async function fetchProfilePic(jid) {
        if (profilePics[jid] !== undefined) return profilePics[jid];
        try {
            var response = await fetch('/instances/' + instanceName + '/profile-picture', {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ jid: jid }),
            });
            var data = await response.json();
            profilePics[jid] = data.profilePictureURL || null;
            return profilePics[jid];
        } catch (e) {
            profilePics[jid] = null;
            return null;
        }
    }

    async function loadProfilePics() {
        var promises = [];
        Object.keys(allContacts).forEach(function(jid) {
            if (jid.includes('@s.whatsapp.net') && jid !== 'status@broadcast') {
                promises.push(fetchProfilePic(jid).then(function(url) {
                    allContacts[jid].profilePic = url;
                }));
            }
        });
        await Promise.all(promises);
        renderContacts();
    }

    async function loadContacts() {
        try {
            const response = await fetch(`/instances/${instanceName}/messages?limit=200`);
            const data = await response.json();

            if (data.messages && data.messages.records) {
                allContacts = {};
                data.messages.records.reverse().forEach(msg => {
                    const jid = msg.keyRemoteJid;
                    if (!jid) return;
                    const displayJid = normalizeJid(jid);
                    if (!allContacts[displayJid]) {
                        allContacts[displayJid] = { displayJid, name: formatJidName(displayJid), lastMessage: '', time: '', unread: 0 };
                    }
                    allContacts[displayJid].lastMessage = parseContent(msg);
                    allContacts[displayJid].time = formatTime(msg.messageTimestamp);
                });
                renderContacts();
                loadProfilePics();
            }
        } catch (error) {
            console.error('Error loading contacts:', error);
        }
    }

    function renderContacts() {
        const container = document.getElementById('contacts-list');
        const sorted = Object.values(allContacts).sort((a, b) => {
            const timeA = a.time ? new Date('2026-01-01 ' + a.time) : new Date(0);
            const timeB = b.time ? new Date('2026-01-01 ' + b.time) : new Date(0);
            return timeB - timeA;
        });

        container.innerHTML = sorted.map(function(c) {
            var initial = getInitial(c.name);
            var avatar = c.profilePic
                ? '<img src="' + c.profilePic + '" class="w-12 h-12 rounded-full object-cover mr-3 flex-shrink-0">'
                : '<div class="w-12 h-12 rounded-full bg-gradient-to-br from-green-400 to-green-600 flex items-center justify-center mr-3 flex-shrink-0"><span class="text-white font-bold text-lg">' + initial + '</span></div>';
            var badge = c.unread > 0 ? '<span class="unread-badge">' + c.unread + '</span>' : '';
            var activeClass = c.displayJid === currentChat ? ' active' : '';

            return '<div class="contact-item' + activeClass + '" onclick="selectChat(\'' + c.displayJid + '\')">' +
                '<div class="flex items-center">' + avatar +
                '<div class="flex-1 min-w-0">' +
                    '<div class="flex justify-between items-center">' +
                        '<span class="font-medium text-gray-800 dark:text-white text-sm truncate">' + c.name + '</span>' +
                        '<span class="text-xs text-gray-500 dark:text-gray-400">' + (c.time || '') + '</span>' +
                    '</div>' +
                    '<div class="flex justify-between items-center">' +
                        '<p class="text-xs text-gray-500 dark:text-gray-400 truncate max-w-[200px]">' + escapeHtml(c.lastMessage || 'Nenhuma mensagem') + '</p>' +
                        badge +
                    '</div>' +
                '</div></div></div>';
        }).join('');
    }

    function filterContacts() {
        const search = document.getElementById('search-contacts').value.toLowerCase();
        document.querySelectorAll('.contact-item').forEach(item => {
            const name = item.textContent.toLowerCase();
            item.style.display = name.includes(search) ? 'flex' : 'none';
        });
    }

    async function selectChat(displayJid) {
        currentChat = displayJid;
        var number = displayJid.replace('@s.whatsapp.net', '').replace('@g.us', '');
        document.getElementById('chat-number').value = number;
        document.getElementById('chat-name').textContent = formatJidName(displayJid);
        document.getElementById('chat-status').textContent = 'online';
        document.getElementById('chat-input-area').style.display = 'block';

        // Update avatar in header
        var avatarContainer = document.getElementById('chat-avatar');
        var pic = allContacts[displayJid] ? allContacts[displayJid].profilePic : null;
        if (pic) {
            avatarContainer.innerHTML = '<img src="' + pic + '" class="w-10 h-10 rounded-full object-cover">';
        } else {
            avatarContainer.innerHTML = '<span class="text-white font-bold text-lg">' + getInitial(formatJidName(displayJid)) + '</span>';
        }

        // Reset unread
        if (allContacts[displayJid]) allContacts[displayJid].unread = 0;
        renderContacts();

        await loadChat(true);
    }

    async function loadChat(initial) {
        if (!currentChat) return;

        const container = document.getElementById('chat-messages');

        if (initial) {
            container.innerHTML = '<div class="flex justify-center py-4"><div class="typing-indicator"><div class="typing-dot"></div><div class="typing-dot"></div><div class="typing-dot"></div></div></div>';
            lastMessageId = 0;
        }

        try {
            const response = await fetch('/instances/' + instanceName + '/messages?limit=100', {
                credentials: 'same-origin',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                }
            });

            if (!response.ok) return;

            const data = await response.json();

            if (data.messages && data.messages.records) {
                var filtered = data.messages.records.filter(function(msg) {
                    if (!msg.keyRemoteJid) return false;
                    return normalizeJid(msg.keyRemoteJid) === currentChat;
                });

                if (initial) {
                    renderMessages(filtered.reverse());
                } else {
                    // Only append new messages and update contacts
                    filtered.forEach(function(msg) {
                        if (msg.id > lastMessageId) {
                            appendMessage(msg);
                            lastMessageId = msg.id;

                            // Update contact's last message
                            var contactJid = normalizeJid(msg.keyRemoteJid);
                            if (allContacts[contactJid]) {
                                allContacts[contactJid].lastMessage = parseContent(msg);
                                allContacts[contactJid].time = formatTime(msg.messageTimestamp);
                                if (!msg.keyFromMe && contactJid !== currentChat) {
                                    allContacts[contactJid].unread = (allContacts[contactJid].unread || 0) + 1;
                                }
                                renderContacts();
                            }
                        }
                    });
                }

                if (filtered.length > 0) {
                    lastMessageId = Math.max(lastMessageId, filtered[filtered.length - 1].id);
                }
            }
        } catch (error) {
            // Silent fail for polling
        }
    }

    function renderMessages(messages) {
        const container = document.getElementById('chat-messages');

        console.log('renderMessages called with', messages.length, 'messages');

        if (!messages || messages.length === 0) {
            container.innerHTML = '<div class="flex items-center justify-center h-full text-gray-500"><p>Nenhuma mensagem encontrada para esta conversa</p></div>';
            return;
        }

        let html = '';
        let lastDate = '';

        messages.forEach(function(msg) {
            var msgDate = msg.messageTimestamp ? new Date(msg.messageTimestamp * 1000).toLocaleDateString('pt-BR') : '';
            if (msgDate !== lastDate) {
                html += '<div class="flex justify-center my-3"><span class="bg-white dark:bg-gray-700 text-gray-600 dark:text-gray-300 text-xs px-3 py-1 rounded-full shadow">' + msgDate + '</span></div>';
                lastDate = msgDate;
            }

            var isMe = msg.keyFromMe;
            var content = parseContent(msg);
            var time = msg.messageTimestamp ? new Date(msg.messageTimestamp * 1000).toLocaleTimeString('pt-BR', {hour: '2-digit', minute: '2-digit'}) : '';
            var align = isMe ? 'justify-end' : 'justify-start';
            var bubble = isMe ? 'msg-sent' : 'msg-received';
            var check = isMe ? '<span class="msg-check">&#10003;&#10003;</span>' : '';

            html += '<div class="flex ' + align + ' mb-1">' +
                '<div class="msg-bubble ' + bubble + '">' +
                '<p class="text-sm text-gray-800 dark:text-gray-200 whitespace-pre-wrap">' + escapeHtml(content) + '</p>' +
                '<div class="msg-time">' + time + ' ' + check + '</div>' +
                '</div>' +
            '</div>';
        });

        container.innerHTML = html;
        container.scrollTop = container.scrollHeight;
    }

    function appendMessage(msg) {
        const container = document.getElementById('chat-messages');
        const isMe = msg.keyFromMe;
        const content = parseContent(msg);
        const time = msg.messageTimestamp ? new Date(msg.messageTimestamp * 1000).toLocaleTimeString('pt-BR', {hour: '2-digit', minute: '2-digit'}) : '';
        const align = isMe ? 'justify-end' : 'justify-start';
        const bubble = isMe ? 'msg-sent' : 'msg-received';

        const html = `<div class="flex ${align} mb-1">
            <div class="msg-bubble ${bubble}">
                <p class="text-sm text-gray-800 dark:text-gray-200 whitespace-pre-wrap">${escapeHtml(content)}</p>
                <div class="msg-time">${time} ${isMe ? '<span class="msg-check">✓✓</span>' : ''}</div>
            </div>
        </div>`;

        container.insertAdjacentHTML('beforeend', html);
        container.scrollTop = container.scrollHeight;
    }

    function parseContent(msg) {
        if (!msg.content) return '[mensagem]';
        var content = typeof msg.content === 'string' ? JSON.parse(msg.content) : msg.content;
        var prefix = msg.keyFromMe ? 'Você: ' : '';
        if (content.text) return prefix + content.text;
        if (content.caption) return prefix + '[arquivo] ' + content.caption;
        if (content.conversation) return prefix + content.conversation;
        if (content.extendedTextMessage && content.extendedTextMessage.text) return prefix + content.extendedTextMessage.text;
        return '[' + (msg.messageType || 'mensagem') + ']';
    }

    function formatTime(timestamp) {
        if (!timestamp) return '';
        return new Date(timestamp * 1000).toLocaleTimeString('pt-BR', {hour: '2-digit', minute: '2-digit'});
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    async function sendMessage() {
        const number = document.getElementById('chat-number').value;
        const text = document.getElementById('chat-input').value.trim();

        if (!number || !text) return;

        const input = document.getElementById('chat-input');
        input.value = '';
        input.disabled = true;

        try {
            const csrfToken = '{{ csrf_token() }}';
            await fetch(`/messages/${instanceName}/text`, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ number, text }),
            });
        } catch (error) {
            console.error('Send error:', error);
        } finally {
            input.disabled = false;
            input.focus();
        }
    }

    // Initialize
    loadContacts();
    startPolling();
</script>
@endpush
