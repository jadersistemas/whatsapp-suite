@extends('layouts.app')

@section('title', 'Testes API - ' . $instance->name)

@section('content')
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">
                <i class="fas fa-flask mr-2 text-green-600"></i> Testes da API
            </h1>
            <p class="text-gray-600 mt-1">Instância: <strong>{{ $instance->name }}</strong></p>
        </div>
        <a href="{{ route('whatsapp.show', $instance->name) }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg transition">
            <i class="fas fa-arrow-left mr-2"></i> Voltar
        </a>
    </div>
</div>

{{-- Resultado global --}}
<div id="global-result" class="hidden mb-6">
    <div id="global-result-content" class="p-4 rounded-lg"></div>
</div>

{{-- Tabs --}}
<div class="mb-6">
    <div class="flex flex-wrap gap-2 border-b border-gray-200 pb-2">
        <button onclick="showTab('text')" id="tab-text" class="tab-btn active px-4 py-2 rounded-lg font-medium text-sm transition">
            <i class="fas fa-comment mr-1"></i> Texto
        </button>
        <button onclick="showTab('link')" id="tab-link" class="tab-btn px-4 py-2 rounded-lg font-medium text-sm transition">
            <i class="fas fa-link mr-1"></i> Link
        </button>
        <button onclick="showTab('media')" id="tab-media" class="tab-btn px-4 py-2 rounded-lg font-medium text-sm transition">
            <i class="fas fa-image mr-1"></i> Mídia
        </button>
        <button onclick="showTab('contact')" id="tab-contact" class="tab-btn px-4 py-2 rounded-lg font-medium text-sm transition">
            <i class="fas fa-address-card mr-1"></i> Contato
        </button>
        <button onclick="showTab('location')" id="tab-location" class="tab-btn px-4 py-2 rounded-lg font-medium text-sm transition">
            <i class="fas fa-map-marker-alt mr-1"></i> Localização
        </button>
        <button onclick="showTab('reaction')" id="tab-reaction" class="tab-btn px-4 py-2 rounded-lg font-medium text-sm transition">
            <i class="fas fa-smile mr-1"></i> Reação
        </button>
        <button onclick="showTab('carousel')" id="tab-carousel" class="tab-btn px-4 py-2 rounded-lg font-medium text-sm transition">
            <i class="fas fa-images mr-1"></i> Carousel
        </button>
        <button onclick="showTab('check')" id="tab-check" class="tab-btn px-4 py-2 rounded-lg font-medium text-sm transition">
            <i class="fas fa-search mr-1"></i> Verificar Nº
        </button>
    </div>
</div>

{{-- Campo número comum --}}
<div class="bg-white rounded-xl shadow-md p-6 mb-6">
    <label class="block text-sm font-medium text-gray-700 mb-2">
        Número do Destinatário <span class="text-red-500">*</span>
    </label>
    <div class="flex gap-2">
        <input type="text" id="common-number" placeholder="5511999999999"
            class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
        <span class="bg-gray-100 px-3 py-2 rounded-lg text-gray-500 text-sm">Formato: DDD + Número</span>
    </div>
</div>

{{-- ========== TEXTO ========== --}}
<div id="panel-text" class="tab-panel bg-white rounded-xl shadow-md overflow-hidden">
    <div class="gradient-bg px-6 py-4">
        <h2 class="text-white font-bold text-lg"><i class="fas fa-comment mr-2"></i> Enviar Mensagem de Texto</h2>
    </div>
    <div class="p-6">
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Mensagem <span class="text-red-500">*</span></label>
            <textarea id="text-message" rows="5" placeholder="Digite sua mensagem aqui..."
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"></textarea>
        </div>
        <div class="flex items-center gap-4 mb-4">
            <label class="flex items-center">
                <input type="checkbox" id="text-delay" class="rounded border-gray-300 text-green-600">
                <span class="ml-2 text-sm text-gray-700">Com delay (2s)</span>
            </label>
            <label class="flex items-center">
                <input type="checkbox" id="text-presence" class="rounded border-gray-300 text-green-600">
                <span class="ml-2 text-sm text-gray-700">Mostrar "digitando..."</span>
            </label>
        </div>
        <button onclick="sendTest('text')" class="bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-lg transition">
            <i class="fas fa-paper-plane mr-2"></i> Enviar Texto
        </button>
    </div>
</div>

{{-- ========== LINK ========== --}}
<div id="panel-link" class="tab-panel hidden bg-white rounded-xl shadow-md overflow-hidden">
    <div class="bg-blue-600 px-6 py-4">
        <h2 class="text-white font-bold text-lg"><i class="fas fa-link mr-2"></i> Enviar Link com Preview</h2>
    </div>
    <div class="p-6">
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">URL do Link <span class="text-red-500">*</span></label>
            <input type="url" id="link-url" placeholder="https://exemplo.com"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Texto Opcional</label>
            <input type="text" id="link-text" placeholder="Texto descritivo do link"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        </div>
        <button onclick="sendTest('link')" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg transition">
            <i class="fas fa-link mr-2"></i> Enviar Link
        </button>
    </div>
</div>

{{-- ========== MÍDIA ========== --}}
<div id="panel-media" class="tab-panel hidden bg-white rounded-xl shadow-md overflow-hidden">
    <div class="bg-orange-600 px-6 py-4">
        <h2 class="text-white font-bold text-lg"><i class="fas fa-image mr-2"></i> Enviar Mídia</h2>
    </div>
    <div class="p-6">
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Arquivo <span class="text-red-500">*</span></label>
            <input type="file" id="media-file" accept="image/*,video/*,application/pdf"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-orange-100 file:text-orange-700 file:font-semibold hover:file:bg-orange-200">
            <p class="mt-1 text-sm text-gray-500">Imagens, vídeos ou PDF (máx 16MB)</p>
            <div id="media-preview" class="hidden mt-3">
                <img id="media-preview-img" src="" alt="Preview" class="max-h-48 rounded-lg border">
            </div>
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Legenda</label>
            <input type="text" id="media-caption" placeholder="Legenda da mídia"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
        </div>
        <button onclick="sendTest('media')" id="media-send-btn" class="bg-orange-600 hover:bg-orange-700 text-white font-bold py-3 px-6 rounded-lg transition">
            <i class="fas fa-image mr-2"></i> Enviar Mídia
        </button>
    </div>
</div>

{{-- ========== CONTATO ========== --}}
<div id="panel-contact" class="tab-panel hidden bg-white rounded-xl shadow-md overflow-hidden">
    <div class="bg-teal-600 px-6 py-4">
        <h2 class="text-white font-bold text-lg"><i class="fas fa-address-card mr-2"></i> Enviar Contato</h2>
    </div>
    <div class="p-6">
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Nome do Contato <span class="text-red-500">*</span></label>
            <input type="text" id="contact-name" placeholder="Nome do contato"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500">
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Telefones (separados por vírgula)</label>
            <input type="text" id="contact-phones" placeholder="5511999999999, 5511888888888"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500">
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">E-mails (separados por vírgula)</label>
            <input type="text" id="contact-emails" placeholder="email@exemplo.com"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500">
        </div>
        <button onclick="sendTest('contact')" class="bg-teal-600 hover:bg-teal-700 text-white font-bold py-3 px-6 rounded-lg transition">
            <i class="fas fa-address-card mr-2"></i> Enviar Contato
        </button>
    </div>
</div>

{{-- ========== LOCALIZAÇÃO ========== --}}
<div id="panel-location" class="tab-panel hidden bg-white rounded-xl shadow-md overflow-hidden">
    <div class="bg-red-600 px-6 py-4">
        <h2 class="text-white font-bold text-lg"><i class="fas fa-map-marker-alt mr-2"></i> Enviar Localização</h2>
    </div>
    <div class="p-6">
        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Latitude <span class="text-red-500">*</span></label>
                <input type="text" id="loc-lat" placeholder="-3.7319" value="-3.7319"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Longitude <span class="text-red-500">*</span></label>
                <input type="text" id="loc-lng" placeholder="-38.5126" value="-38.5126"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500">
            </div>
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Nome do Local</label>
            <input type="text" id="loc-name" placeholder="Ex: Praça do Ferreira"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500">
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Endereço</label>
            <input type="text" id="loc-address" placeholder="Rua Exemplo, 123"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500">
        </div>
        <button onclick="sendTest('location')" class="bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-6 rounded-lg transition">
            <i class="fas fa-map-marker-alt mr-2"></i> Enviar Localização
        </button>
    </div>
</div>

{{-- ========== REAÇÃO ========== --}}
<div id="panel-reaction" class="tab-panel hidden bg-white rounded-xl shadow-md overflow-hidden">
    <div class="bg-yellow-500 px-6 py-4">
        <h2 class="text-white font-bold text-lg"><i class="fas fa-smile mr-2"></i> Enviar Reação</h2>
    </div>
    <div class="p-6">
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">ID da Mensagem <span class="text-red-500">*</span></label>
            <input type="text" id="reaction-msgid" placeholder="3EB0ABC123DEF456"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500">
            <p class="mt-1 text-sm text-gray-500">ID da mensagem para reagir (obtido via webhook)</p>
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Emoji <span class="text-red-500">*</span></label>
            <div class="flex gap-2 flex-wrap">
                @foreach(['👍', '❤️', '😂', '😮', '😢', '🙏', '🔥', '👏', '🎉', '💯'] as $emoji)
                    <button type="button" onclick="document.getElementById('reaction-emoji').value='{{ $emoji }}'; document.querySelectorAll('.emoji-btn').forEach(e=>e.classList.remove('ring-2','ring-yellow-500')); this.classList.add('ring-2','ring-yellow-500')"
                        class="emoji-btn text-2xl p-2 hover:bg-gray-100 rounded-lg transition">{{ $emoji }}</button>
                @endforeach
            </div>
            <input type="hidden" id="reaction-emoji" value="👍">
        </div>
        <button onclick="sendTest('reaction')" class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-3 px-6 rounded-lg transition">
            <i class="fas fa-smile mr-2"></i> Enviar Reação
        </button>
    </div>
</div>

{{-- ========== CAROUSEL ========== --}}
<div id="panel-carousel" class="tab-panel hidden bg-white rounded-xl shadow-md overflow-hidden">
    <div class="bg-indigo-600 px-6 py-4">
        <h2 class="text-white font-bold text-lg"><i class="fas fa-images mr-2"></i> Enviar Carousel</h2>
    </div>
    <div class="p-6">
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Texto da Mensagem <span class="text-red-500">*</span></label>
            <input type="text" id="carousel-text" placeholder="Confira nossos produtos:"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Cards (máx. 10)</label>
            <div id="cards-list" class="space-y-4">
                <div class="card-row bg-gray-50 p-4 rounded-lg border border-gray-200">
                    <div class="flex justify-between items-center mb-3">
                        <span class="font-medium text-gray-700">Card 1</span>
                        <button onclick="removeCard(this)" class="text-red-500 hover:text-red-700 text-sm">
                            <i class="fas fa-trash mr-1"></i> Remover
                        </button>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">URL da Imagem *</label>
                            <input type="url" class="card-image px-3 py-2 w-full border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500" placeholder="https://...">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">Título *</label>
                            <input type="text" class="card-title px-3 py-2 w-full border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500" placeholder="Nome do produto" maxlength="20">
                        </div>
                    </div>
                    <div class="mt-3">
                        <label class="block text-xs text-gray-500 mb-1">Botões (máx. 3)</label>
                        <div class="card-buttons space-y-2">
                            <div class="flex gap-2 items-center">
                                <select class="btn-type px-2 py-1 border border-gray-300 rounded text-sm">
                                    <option value="quick_reply">Resposta</option>
                                    <option value="url">URL</option>
                                </select>
                                <input type="text" class="btn-id px-2 py-1 border border-gray-300 rounded text-sm w-20" placeholder="ID">
                                <input type="text" class="btn-text px-2 py-1 border border-gray-300 rounded text-sm flex-1" placeholder="Texto do botão" maxlength="20">
                                <input type="url" class="btn-url px-2 py-1 border border-gray-300 rounded text-sm flex-1 hidden" placeholder="https://...">
                                <button onclick="removeCardButton(this)" class="text-red-500 hover:text-red-700 text-xs">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                        <button onclick="addCardButton(this)" class="mt-2 text-indigo-600 hover:text-indigo-800 text-sm">
                            <i class="fas fa-plus mr-1"></i> Adicionar Botão
                        </button>
                    </div>
                </div>
            </div>
            <button onclick="addCard()" id="add-card" class="mt-3 bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium py-2 px-4 rounded-lg transition">
                <i class="fas fa-plus mr-1"></i> Adicionar Card
            </button>
        </div>

        <button onclick="sendTest('carousel')" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-6 rounded-lg transition">
            <i class="fas fa-images mr-2"></i> Enviar Carousel
        </button>
    </div>
</div>

{{-- ========== VERIFICAR NÚMERO ========== --}}
<div id="panel-check" class="tab-panel hidden bg-white rounded-xl shadow-md overflow-hidden">
    <div class="bg-purple-600 px-6 py-4">
        <h2 class="text-white font-bold text-lg"><i class="fas fa-search mr-2"></i> Verificar Número no WhatsApp</h2>
    </div>
    <div class="p-6">
        <button onclick="sendTest('check')" class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-3 px-6 rounded-lg transition">
            <i class="fas fa-search mr-2"></i> Verificar Número
        </button>
    </div>
</div>

@endsection

@push('scripts')
<style>
    .tab-btn { color: #6b7280; background: #f3f4f6; }
    .tab-btn.active { color: white; background: #16a34a; }
    .tab-btn:hover:not(.active) { background: #e5e7eb; }
</style>

<script>
    const instanceName = '{{ $instance->name }}';

    function showTab(tab) {
        document.querySelectorAll('.tab-panel').forEach(p => p.classList.add('hidden'));
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        document.getElementById('panel-' + tab).classList.remove('hidden');
        document.getElementById('tab-' + tab).classList.add('active');
    }

    function showResult(success, message, data) {
        const el = document.getElementById('global-result');
        const content = document.getElementById('global-result-content');
        el.classList.remove('hidden');

        if (success) {
            content.className = 'p-4 rounded-lg bg-green-100 border border-green-300';
            content.innerHTML = `<div class="flex items-center"><i class="fas fa-check-circle text-green-600 text-xl mr-3"></i><div><p class="font-bold text-green-800">${message}</p>${data ? '<pre class="mt-2 text-xs text-green-700 bg-green-50 p-2 rounded overflow-auto max-h-48">' + JSON.stringify(data, null, 2) + '</pre>' : ''}</div></div>`;
        } else {
            content.className = 'p-4 rounded-lg bg-red-100 border border-red-300';
            content.innerHTML = `<div class="flex items-center"><i class="fas fa-times-circle text-red-600 text-xl mr-3"></div><div><p class="font-bold text-red-800">${message}</p>${data ? '<pre class="mt-2 text-xs text-red-700 bg-red-50 p-2 rounded overflow-auto max-h-48">' + JSON.stringify(data, null, 2) + '</pre>' : ''}</div></div>`;
        }

        setTimeout(() => el.classList.add('hidden'), 15000);
    }

    function getNumber() {
        return document.getElementById('common-number').value.trim();
    }

    async function sendTest(type) {
        const number = getNumber();
        if (type !== 'check' && !number) {
            showResult(false, 'Digite o número do destinatário');
            return;
        }

        let body = { number };

        try {
            if (type === 'text') {
                const text = document.getElementById('text-message').value;
                if (!text) { showResult(false, 'Digite a mensagem'); return; }
                body.text = text;
                if (document.getElementById('text-delay').checked) body.delay = 2000;
                if (document.getElementById('text-presence').checked) body.presence = 'composing';
            }
            else if (type === 'link') {
                body.url = document.getElementById('link-url').value;
                body.text = document.getElementById('link-text').value;
                if (!body.url) { showResult(false, 'Digite a URL'); return; }
            }
            else if (type === 'media') {
                const file = document.getElementById('media-file').files[0];
                if (!file) { showResult(false, 'Selecione um arquivo'); return; }
                if (file.size > 16 * 1024 * 1024) { showResult(false, 'Arquivo muito grande (máx 16MB)'); return; }

                showResult(true, 'Enviando arquivo...');

                const formData = new FormData();
                formData.append('number', number);
                formData.append('attachment', file);
                formData.append('mediaType', file.type || 'application/octet-stream');
                formData.append('caption', document.getElementById('media-caption').value);

                doSendMedia(formData);
                return;
            }
            else if (type === 'contact') {
                body.contact_name = document.getElementById('contact-name').value;
                body.phones = document.getElementById('contact-phones').value;
                body.emails = document.getElementById('contact-emails').value;
                if (!body.contact_name) { showResult(false, 'Digite o nome do contato'); return; }
            }
            else if (type === 'location') {
                body.latitude = parseFloat(document.getElementById('loc-lat').value);
                body.longitude = parseFloat(document.getElementById('loc-lng').value);
                body.name = document.getElementById('loc-name').value;
                body.address = document.getElementById('loc-address').value;
                if (isNaN(body.latitude) || isNaN(body.longitude)) { showResult(false, 'Coordenadas inválidas'); return; }
            }
            else if (type === 'reaction') {
                body.message_id = document.getElementById('reaction-msgid').value;
                body.emoji = document.getElementById('reaction-emoji').value;
                if (!body.message_id) { showResult(false, 'Digite o ID da mensagem'); return; }
            }
            else if (type === 'carousel') {
                body.text = document.getElementById('carousel-text').value;
                if (!body.text) { showResult(false, 'Digite o texto da mensagem'); return; }

                const cardRows = document.querySelectorAll('.card-row');
                const cards = [];
                cardRows.forEach(cardRow => {
                    const imageUrl = cardRow.querySelector('.card-image').value;
                    const title = cardRow.querySelector('.card-title').value;

                    if (!imageUrl) { showResult(false, 'Preencha a URL da imagem de todos os cards'); return; }
                    if (!title) { showResult(false, 'Preencha o título de todos os cards'); return; }

                    const card = { imageUrl, title, buttons: [] };
                    const btnRows = cardRow.querySelectorAll('.card-buttons .flex');
                    btnRows.forEach(btnRow => {
                        const btnType = btnRow.querySelector('.btn-type').value;
                        const btnId = btnRow.querySelector('.btn-id').value;
                        const btnText = btnRow.querySelector('.btn-text').value;
                        const btnUrl = btnRow.querySelector('.btn-url').value;

                        if (btnText) {
                            const btn = { type: btnType, text: btnText };
                            if (btnType === 'quick_reply') {
                                if (btnId) btn.id = btnId;
                            } else {
                                if (btnUrl) btn.url = btnUrl;
                            }
                            card.buttons.push(btn);
                        }
                    });

                    cards.push(card);
                });

                if (cards.length === 0) { showResult(false, 'Adicione pelo menos 1 card'); return; }

                body.cards = cards;
            }
            else if (type === 'check') {
                body.number = number;
                if (!number) { showResult(false, 'Digite o número para verificar'); return; }
            }

            const url = type === 'check' ? `/api/check-number/${instanceName}` : `/messages/${instanceName}/${type}`;
            const method = type === 'check' ? 'POST' : 'POST';

            const response = await fetch(url, {
                method,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                body: JSON.stringify(body),
            });

            const data = await response.json();

            if (response.ok) {
                showResult(true, 'Operação realizada com sucesso!', data);
            } else {
                showResult(false, 'Erro: ' + (data.error || data.message || JSON.stringify(data)), data);
            }
        } catch (error) {
            showResult(false, 'Erro de conexão: ' + error.message);
        }
    }

    async function doSendMedia(formData) {
        const btn = document.getElementById('media-send-btn');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Enviando...';

        try {
            const response = await fetch(`/messages/${instanceName}/media`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
                body: formData,
            });
            const data = await response.json();
            if (response.ok) {
                showResult(true, 'Mídia enviada com sucesso!', data);
            } else {
                showResult(false, 'Erro: ' + (data.error || data.message || JSON.stringify(data)), data);
            }
        } catch (error) {
            showResult(false, 'Erro de conexão: ' + error.message);
        } finally {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-image mr-2"></i> Enviar Mídia';
        }
    }

    document.getElementById('media-file').addEventListener('change', function(e) {
        const file = e.target.files[0];
        const preview = document.getElementById('media-preview');
        const previewImg = document.getElementById('media-preview-img');
        if (!file) { preview.classList.add('hidden'); return; }
        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(ev) {
                previewImg.src = ev.target.result;
                preview.classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        } else {
            previewImg.src = '';
            preview.classList.add('hidden');
        }
    });

    function addCard() {
        const list = document.getElementById('cards-list');
        if (list.children.length >= 10) return;

        const cardNum = list.children.length + 1;
        const card = document.createElement('div');
        card.className = 'card-row bg-gray-50 p-4 rounded-lg border border-gray-200';
        card.innerHTML = `
            <div class="flex justify-between items-center mb-3">
                <span class="font-medium text-gray-700">Card ${cardNum}</span>
                <button onclick="removeCard(this)" class="text-red-500 hover:text-red-700 text-sm">
                    <i class="fas fa-trash mr-1"></i> Remover
                </button>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs text-gray-500 mb-1">URL da Imagem *</label>
                    <input type="url" class="card-image px-3 py-2 w-full border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500" placeholder="https://...">
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Título *</label>
                    <input type="text" class="card-title px-3 py-2 w-full border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500" placeholder="Nome do produto" maxlength="20">
                </div>
            </div>
            <div class="mt-3">
                <label class="block text-xs text-gray-500 mb-1">Botões (máx. 3)</label>
                <div class="card-buttons space-y-2">
                    <div class="flex gap-2 items-center">
                        <select class="btn-type px-2 py-1 border border-gray-300 rounded text-sm">
                            <option value="quick_reply">Resposta</option>
                            <option value="url">URL</option>
                        </select>
                        <input type="text" class="btn-id px-2 py-1 border border-gray-300 rounded text-sm w-20" placeholder="ID">
                        <input type="text" class="btn-text px-2 py-1 border border-gray-300 rounded text-sm flex-1" placeholder="Texto do botão" maxlength="20">
                        <input type="url" class="btn-url px-2 py-1 border border-gray-300 rounded text-sm flex-1 hidden" placeholder="https://...">
                        <button onclick="removeCardButton(this)" class="text-red-500 hover:text-red-700 text-xs">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                <button onclick="addCardButton(this)" class="mt-2 text-indigo-600 hover:text-indigo-800 text-sm">
                    <i class="fas fa-plus mr-1"></i> Adicionar Botão
                </button>
            </div>
        `;
        list.appendChild(card);
        updateAddCard();
    }

    function removeCard(btn) {
        btn.closest('.card-row').remove();
        updateAddCard();
    }

    function updateAddCard() {
        const list = document.getElementById('cards-list');
        const addBtn = document.getElementById('add-card');
        addBtn.style.display = list.children.length >= 10 ? 'none' : 'inline-block';
    }

    function addCardButton(btn) {
        const card = btn.closest('.card-row');
        const buttonsList = card.querySelector('.card-buttons');
        if (buttonsList.children.length >= 3) return;

        const row = document.createElement('div');
        row.className = 'flex gap-2 items-center';
        row.innerHTML = `
            <select class="btn-type px-2 py-1 border border-gray-300 rounded text-sm" onchange="toggleCardBtnUrl(this)">
                <option value="quick_reply">Resposta</option>
                <option value="url">URL</option>
            </select>
            <input type="text" class="btn-id px-2 py-1 border border-gray-300 rounded text-sm w-20" placeholder="ID">
            <input type="text" class="btn-text px-2 py-1 border border-gray-300 rounded text-sm flex-1" placeholder="Texto do botão" maxlength="20">
            <input type="url" class="btn-url px-2 py-1 border border-gray-300 rounded text-sm flex-1 hidden" placeholder="https://...">
            <button onclick="removeCardButton(this)" class="text-red-500 hover:text-red-700 text-xs">
                <i class="fas fa-times"></i>
            </button>
        `;
        buttonsList.appendChild(row);
    }

    function removeCardButton(btn) {
        btn.closest('.flex').remove();
    }

    function toggleCardBtnUrl(select) {
        const row = select.closest('.flex');
        const idInput = row.querySelector('.btn-id');
        const urlInput = row.querySelector('.btn-url');

        if (select.value === 'url') {
            idInput.classList.add('hidden');
            urlInput.classList.remove('hidden');
        } else {
            idInput.classList.remove('hidden');
            urlInput.classList.add('hidden');
        }
    }
</script>
@endpush
