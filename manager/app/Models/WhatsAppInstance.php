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
    ];

    protected $hidden = [
        'token',
    ];

    /**
     * Get connection status badge class
     */
    public function getStatusBadgeAttribute(): string
    {
        return match(strtolower($this->status)) {
            'online', 'open' => 'bg-green-100 text-green-800',
            'offline' => 'bg-red-100 text-red-800',
            'connecting' => 'bg-yellow-100 text-yellow-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }
}
