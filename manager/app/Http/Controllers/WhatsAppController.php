<?php

namespace App\Http\Controllers;

use App\Models\WhatsAppInstance;
use App\Services\WhatsAppApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WhatsAppController extends Controller
{
    private WhatsAppApiService $api;

    public function __construct(WhatsAppApiService $api)
    {
        $this->api = $api;
    }

    /**
     * Dashboard
     */
    public function dashboard()
    {
        $instances = WhatsAppInstance::all();
        $stats = [
            'total' => $instances->count(),
            'online' => $instances->whereIn('status', ['ONLINE', 'open', 'OPEN'])->count(),
            'offline' => $instances->whereIn('status', ['OFFLINE', 'offline'])->count(),
            'connecting' => $instances->whereIn('status', ['CONNECTING', 'connecting'])->count(),
        ];

        return view('dashboard', compact('instances', 'stats'));
    }

    /**
     * List instances
     */
    public function instances()
    {
        $instances = WhatsAppInstance::all();
        return view('instances.index', compact('instances'));
    }

    /**
     * Create instance form
     */
    public function createInstance()
    {
        return view('instances.create');
    }

    /**
     * Store new instance
     */
    public function storeInstance(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:whatsapp_instances,name',
            'description' => 'nullable|string|max:500',
        ]);

        $result = $this->api->createInstance($request->name, $request->description ?? '');

        if ($result['success']) {
            WhatsAppInstance::create([
                'name' => $request->name,
                'description' => $request->description,
                'token' => $result['data']['Auth']['token'] ?? $result['data']['auth']['token'] ?? '',
                'status' => 'OFFLINE',
            ]);

            return redirect()->route('whatsapp.instances')
                ->with('success', "Instância '{$request->name}' criada com sucesso!");
        }

        return back()->with('error', 'Erro ao criar instância: ' . ($result['error'] ?? 'Erro desconhecido'));
    }

    /**
     * Show instance details
     */
    public function showInstance(string $name)
    {
        $instance = WhatsAppInstance::where('name', $name)->firstOrFail();
        $status = $this->api->getConnectionState($name);
        $webhook = $this->api->getWebhook($name);

        return view('instances.show', compact('instance', 'status', 'webhook'));
    }

    /**
     * Connect instance via QR Code
     */
    public function connectQr(string $name)
    {
        $result = $this->api->connectQr($name);

        if ($result['success']) {
            return response()->json($result['data']);
        }

        return response()->json(['error' => $result['error']], 400);
    }

    /**
     * Connect instance via pairing code
     */
    public function connectPairing(Request $request, string $name)
    {
        $request->validate([
            'phone' => 'required|string|min:10|max:15',
        ]);

        $result = $this->api->connectPairingCode($name, $request->phone);

        if ($result['success']) {
            return response()->json($result['data']);
        }

        return response()->json(['error' => $result['error']], 400);
    }

    /**
     * Get connection state
     */
    public function connectionState(string $name)
    {
        $result = $this->api->getConnectionState($name);

        if ($result['success']) {
            $status = $result['data']['state'] ?? 'UNKNOWN';
            WhatsAppInstance::where('name', $name)->update(['status' => $status]);

            return response()->json($result['data']);
        }

        return response()->json(['error' => $result['error']], 400);
    }

    /**
     * Logout instance
     */
    public function logout(string $name)
    {
        $result = $this->api->logout($name);

        if ($result['success']) {
            WhatsAppInstance::where('name', $name)->update(['status' => 'OFFLINE']);
            return redirect()->route('whatsapp.instances')
                ->with('success', "Instância '{$name}' desconectada.");
        }

        return back()->with('error', 'Erro ao desconectar: ' . ($result['error'] ?? 'Erro desconhecido'));
    }

    /**
     * Delete instance
     */
    public function deleteInstance(string $name)
    {
        $result = $this->api->deleteInstance($name);

        if ($result['success']) {
            WhatsAppInstance::where('name', $name)->delete();
            return redirect()->route('whatsapp.instances')
                ->with('success', "Instância '{$name}' removida.");
        }

        return back()->with('error', 'Erro ao remover: ' . ($result['error'] ?? 'Erro desconhecido'));
    }

    /**
     * Send message page
     */
    public function sendMessage(string $name)
    {
        $instance = WhatsAppInstance::where('name', $name)->firstOrFail();
        return view('messages.send', compact('instance'));
    }

    /**
     * Send text message
     */
    public function sendText(Request $request, string $name)
    {
        $request->validate([
            'number' => 'required|string|min:10|max:15',
            'text' => 'required|string|max:4096',
        ]);

        $result = $this->api->sendText($name, $request->number, $request->text);

        if ($result['success']) {
            return $request->expectsJson()
                ? response()->json(['success' => true, 'message' => 'Mensagem enviada com sucesso!', 'data' => $result['data']])
                : back()->with('success', 'Mensagem enviada com sucesso!');
        }

        return $request->expectsJson()
            ? response()->json(['success' => false, 'error' => $result['error'] ?? 'Erro desconhecido'], 400)
            : back()->with('error', 'Erro ao enviar: ' . ($result['error'] ?? 'Erro desconhecido'));
    }

    /**
     * Send link message
     */
    public function sendLink(Request $request, string $name)
    {
        $request->validate([
            'number' => 'required|string|min:10|max:15',
            'url' => 'required|url',
            'text' => 'nullable|string|max:1024',
        ]);

        $result = $this->api->sendLink($name, $request->number, $request->url, $request->text ?? '');

        if ($result['success']) {
            return $request->expectsJson()
                ? response()->json(['success' => true, 'message' => 'Link enviado com sucesso!', 'data' => $result['data']])
                : back()->with('success', 'Link enviado com sucesso!');
        }

        return $request->expectsJson()
            ? response()->json(['success' => false, 'error' => $result['error'] ?? 'Erro desconhecido'], 400)
            : back()->with('error', 'Erro ao enviar: ' . ($result['error'] ?? 'Erro desconhecido'));
    }

    /**
     * Send media (file upload)
     */
    public function sendMedia(Request $request, string $name)
    {
        $request->validate([
            'number' => 'required|string|min:10|max:15',
            'attachment' => 'required|file|max:16384',
        ]);

        $file = $request->file('attachment');
        $mimeType = $file->getMimeType();
        $caption = $request->input('caption') ?? '';

        // Map MIME type to API media type
        $mediaType = 'document';
        if (str_starts_with($mimeType, 'image/')) {
            $mediaType = 'image';
        } elseif (str_starts_with($mimeType, 'video/')) {
            $mediaType = 'video';
        } elseif (str_starts_with($mimeType, 'audio/')) {
            $mediaType = 'audio';
        }

        $result = $this->api->sendMediaFile($name, $request->number, $file->getRealPath(), $mediaType, $caption);

        if ($result['success']) {
            return $request->expectsJson()
                ? response()->json(['success' => true, 'message' => 'Mídia enviada com sucesso!', 'data' => $result['data']])
                : back()->with('success', 'Mídia enviada com sucesso!');
        }

        return $request->expectsJson()
            ? response()->json(['success' => false, 'error' => $result['error'] ?? 'Erro desconhecido'], 400)
            : back()->with('error', 'Erro ao enviar mídia: ' . ($result['error'] ?? 'Erro desconhecido'));
    }

    /**
     * Send contact
     */
    public function sendContact(Request $request, string $name)
    {
        $request->validate([
            'number' => 'required|string|min:10|max:15',
            'contact_name' => 'required|string|max:255',
            'phones' => 'nullable|string',
            'emails' => 'nullable|string',
        ]);

        $phones = array_filter(explode(',', $request->phones ?? ''));
        $emails = array_filter(explode(',', $request->emails ?? ''));

        $result = $this->api->sendContact($name, $request->number, $request->contact_name, $phones, $emails);

        if ($result['success']) {
            return $request->expectsJson()
                ? response()->json(['success' => true, 'message' => 'Contato enviado com sucesso!', 'data' => $result['data']])
                : back()->with('success', 'Contato enviado com sucesso!');
        }

        return $request->expectsJson()
            ? response()->json(['success' => false, 'error' => $result['error'] ?? 'Erro desconhecido'], 400)
            : back()->with('error', 'Erro ao enviar contato: ' . ($result['error'] ?? 'Erro desconhecido'));
    }

    /**
     * Send location
     */
    public function sendLocation(Request $request, string $name)
    {
        $request->validate([
            'number' => 'required|string|min:10|max:15',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'name' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:500',
        ]);

        $result = $this->api->sendLocation($name, $request->number, $request->latitude, $request->longitude, $request->name ?? '', $request->address ?? '');

        if ($result['success']) {
            return $request->expectsJson()
                ? response()->json(['success' => true, 'message' => 'Localização enviada com sucesso!', 'data' => $result['data']])
                : back()->with('success', 'Localização enviada com sucesso!');
        }

        return $request->expectsJson()
            ? response()->json(['success' => false, 'error' => $result['error'] ?? 'Erro desconhecido'], 400)
            : back()->with('error', 'Erro ao enviar localização: ' . ($result['error'] ?? 'Erro desconhecido'));
    }

    /**
     * Send reaction
     */
    public function sendReaction(Request $request, string $name)
    {
        $request->validate([
            'message_id' => 'required|string',
            'emoji' => 'required|string|max:10',
        ]);

        $result = $this->api->sendReaction($name, $request->message_id, $request->emoji);

        if ($result['success']) {
            return $request->expectsJson()
                ? response()->json(['success' => true, 'message' => 'Reação enviada com sucesso!', 'data' => $result['data']])
                : back()->with('success', 'Reação enviada com sucesso!');
        }

        return $request->expectsJson()
            ? response()->json(['success' => false, 'error' => $result['error'] ?? 'Erro desconhecido'], 400)
            : back()->with('error', 'Erro ao enviar reação: ' . ($result['error'] ?? 'Erro desconhecido'));
    }

    /**
     * Webhook settings page
     */
    public function webhookSettings(string $name)
    {
        $instance = WhatsAppInstance::where('name', $name)->firstOrFail();
        $webhook = $this->api->getWebhook($name);

        return view('webhook.settings', compact('instance', 'webhook'));
    }

    /**
     * Update webhook
     */
    public function updateWebhook(Request $request, string $name)
    {
        $request->validate([
            'url' => 'required|url',
            'enabled' => 'boolean',
            'events.qrcodeUpdated' => 'boolean',
            'events.connectionUpdated' => 'boolean',
            'events.messagesUpsert' => 'boolean',
            'events.sendMessage' => 'boolean',
        ]);

        $events = [
            'qrcodeUpdated' => $request->boolean('events.qrcodeUpdated'),
            'connectionUpdated' => $request->boolean('events.connectionUpdated'),
            'messagesUpsert' => $request->boolean('events.messagesUpsert'),
            'sendMessage' => $request->boolean('events.sendMessage'),
        ];

        $result = $this->api->setWebhook($name, $request->url, $request->boolean('enabled'), $events);

        if ($result['success']) {
            return back()->with('success', 'Webhook atualizado com sucesso!');
        }

        return back()->with('error', 'Erro ao atualizar webhook: ' . ($result['error'] ?? 'Erro desconhecido'));
    }

    /**
     * Check if number is on WhatsApp
     */
    public function checkNumber(Request $request, string $name)
    {
        $request->validate([
            'number' => 'required|string|min:10|max:15',
        ]);

        $result = $this->api->checkNumber($name, $request->number);

        if ($result['success']) {
            return response()->json($result['data']);
        }

        return response()->json(['error' => $result['error']], 400);
    }

    /**
     * Settings page
     */
    public function settings()
    {
        return view('settings');
    }

    /**
     * Update instance settings
     */
    public function updateSettings(Request $request, string $name)
    {
        $request->validate([
            'rejectCalls' => 'nullable|boolean',
            'ignoreGroups' => 'nullable|boolean',
            'alwaysOnline' => 'nullable|boolean',
            'readMessages' => 'nullable|boolean',
            'syncFullHistory' => 'nullable|boolean',
            'viewStatus' => 'nullable|boolean',
        ]);

        $settings = array_filter([
            'rejectCalls' => $request->boolean('rejectCalls'),
            'ignoreGroups' => $request->boolean('ignoreGroups'),
            'alwaysOnline' => $request->boolean('alwaysOnline'),
            'readMessages' => $request->boolean('readMessages'),
            'syncFullHistory' => $request->boolean('syncFullHistory'),
            'viewStatus' => $request->boolean('viewStatus'),
        ], fn($v) => $v !== null);

        $result = $this->api->updateSettings($name, $settings);

        if ($result['success']) {
            return $request->expectsJson()
                ? response()->json($result)
                : back()->with('success', 'Configurações atualizadas com sucesso!');
        }

        return $request->expectsJson()
            ? response()->json(['error' => $result['error'] ?? 'Erro desconhecido'], 400)
            : back()->with('error', 'Erro ao atualizar configurações: ' . ($result['error'] ?? 'Erro desconhecido'));
    }
}
