<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WhatsAppInstance extends Model
{
    use HasFactory;

    protected $table = 'whatsapp_instances';

    protected $fillable = [
        'name',
        'description',
        'token',
        'status',
        'owner_jid',
        'phone',
        'external_attributes',
    ];

    protected $hidden = [
        'token',
    ];

    protected $casts = [
        'external_attributes' => 'array',
    ];

    /**
     * Get connection status badge class
     */
    public function getStatusBadgeAttribute(): string
    {
        return match(strtolower($this->status)) {
            'online', 'open' => 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-300',
            'offline' => 'bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-300',
            'connecting' => 'bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-300',
            default => 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300',
        };
    }

    /**
     * Get settings from external_attributes
     */
    public function getSettingsAttribute(): array
    {
        $attrs = $this->external_attributes ?? [];
        return [
            'rejectCalls' => $attrs['rejectCalls'] ?? false,
            'rejectCallMessage' => $attrs['rejectCallMessage'] ?? 'Esse número não recebe ligações, por favor envie um texto ou áudio!',
            'ignoreGroups' => $attrs['ignoreGroups'] ?? false,
            'alwaysOnline' => $attrs['alwaysOnline'] ?? false,
            'readMessages' => $attrs['readMessages'] ?? false,
            'syncFullHistory' => $attrs['syncFullHistory'] ?? false,
            'viewStatus' => $attrs['viewStatus'] ?? false,
            'autoReply' => $attrs['autoReply'] ?? false,
            'autoReplyMessage' => $attrs['autoReplyMessage'] ?? 'Olá! No momento não posso atender, mas deixe sua mensagem que retorno em breve!',
            'chatbotEnabled' => $attrs['chatbot']['enabled'] ?? false,
            'chatbotFlows' => $attrs['chatbot']['flows'] ?? [],
        ];
    }
}
