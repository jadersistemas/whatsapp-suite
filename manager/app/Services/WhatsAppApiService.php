<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class WhatsAppApiService
{
    private Client $client;
    private string $apiKey;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => config('services.whatsapp.api_url'),
            'timeout' => 30,
            'headers' => [
                'Content-Type' => 'application/json',
                'apikey' => config('services.whatsapp.api_key'),
            ],
        ]);
        $this->apiKey = config('services.whatsapp.api_key');
    }

    /**
     * Create a new instance
     */
    public function createInstance(string $name, string $description = ''): array
    {
        return $this->request('POST', '/instance/create', [
            'instanceName' => $name,
            'description' => $description,
        ]);
    }

    /**
     * List all instances
     */
    public function listInstances(): array
    {
        return $this->request('GET', '/instance/fetchInstances');
    }

    /**
     * Connect instance via QR Code
     */
    public function connectQr(string $instanceName): array
    {
        return $this->request('GET', "/instance/connect/{$instanceName}", [], $instanceName);
    }

    /**
     * Connect instance via pairing code
     */
    public function connectPairingCode(string $instanceName, string $phone): array
    {
        return $this->request('GET', "/instance/connect/{$instanceName}/code/{$phone}", [], $instanceName);
    }

    /**
     * Get connection state
     */
    public function getConnectionState(string $instanceName): array
    {
        return $this->request('GET', "/instance/connectionState/{$instanceName}", [], $instanceName);
    }

    /**
     * Fetch instance details
     */
    public function fetchInstance(string $instanceName): array
    {
        return $this->request('GET', "/instance/fetchInstance/{$instanceName}", [], $instanceName);
    }

    /**
     * List messages for an instance
     */
    public function listMessages(string $instanceName, string $chatJid = '', int $limit = 50, ?int $cursor = null): array
    {
        $params = ['limit' => $limit];
        if ($chatJid) {
            $params['chatJid'] = $chatJid;
        }
        if ($cursor !== null) {
            $params['cursor'] = $cursor;
        }
        return $this->request('GET', "/message/list/{$instanceName}", $params, $instanceName);
    }

    /**
     * Logout instance
     */
    public function logout(string $instanceName): array
    {
        return $this->request('DELETE', "/instance/logout/{$instanceName}", [], $instanceName);
    }

    /**
     * Delete instance
     */
    public function deleteInstance(string $instanceName): array
    {
        return $this->request('DELETE', "/instance/delete/{$instanceName}", [], $instanceName);
    }

    /**
     * Send text message
     */
    public function sendText(string $instanceName, string $number, string $text): array
    {
        return $this->request('POST', "/message/sendText/{$instanceName}", [
            'number' => $number,
            'textMessage' => ['text' => $text],
        ], $instanceName);
    }

    /**
     * Send link message
     */
    public function sendLink(string $instanceName, string $number, string $url, string $text = ''): array
    {
        return $this->request('POST', "/message/sendLink/{$instanceName}", [
            'number' => $number,
            'linkMessage' => [
                'link' => $url,
                'description' => $text ?: null,
            ],
        ], $instanceName);
    }

    /**
     * Send media message (base64)
     */
    public function sendMedia(string $instanceName, string $number, string $mimetype, string $base64, string $caption = ''): array
    {
        return $this->request('POST', "/message/sendMedia/{$instanceName}", [
            'number' => $number,
            'mediaMessage' => [
                'mediatype' => $mimetype,
                'media' => $base64,
                'caption' => $caption ?: null,
            ],
        ], $instanceName);
    }

    /**
     * Send contact
     */
    public function sendContact(string $instanceName, string $number, string $contactName, array $phones = [], array $emails = []): array
    {
        $contact = [
            'fullName' => $contactName,
            'phoneNumber' => $phones[0] ?? '',
        ];

        return $this->request('POST', "/message/sendContact/{$instanceName}", [
            'number' => $number,
            'contactMessage' => [$contact],
        ], $instanceName);
    }

    /**
     * Send location
     */
    public function sendLocation(string $instanceName, string $number, float $latitude, float $longitude, string $name = '', string $address = ''): array
    {
        return $this->request('POST', "/message/sendLocation/{$instanceName}", [
            'number' => $number,
            'locationMessage' => [
                'latitude' => $latitude,
                'longitude' => $longitude,
                'name' => $name,
                'address' => $address,
            ],
        ], $instanceName);
    }

    /**
     * Send reaction
     */
    public function sendReaction(string $instanceName, string $messageId, string $emoji): array
    {
        return $this->request('POST', "/message/sendReaction/{$instanceName}", [
            'reactionMessage' => [
                'key' => [
                    'remoteJid' => '',
                    'id' => $messageId,
                ],
                'reaction' => $emoji,
            ],
        ], $instanceName);
    }

    /**
     * Set webhook
     */
    public function setWebhook(string $instanceName, string $url, bool $enabled, array $events): array
    {
        return $this->request('PUT', "/webhook/set/{$instanceName}", [
            'url' => $url,
            'enabled' => $enabled,
            'events' => $events,
        ], $instanceName);
    }

    /**
     * Get webhook
     */
    public function getWebhook(string $instanceName): array
    {
        return $this->request('GET', "/webhook/find/{$instanceName}", [], $instanceName);
    }

    /**
     * Send media via file upload (multipart)
     */
    public function sendMediaFile(string $instanceName, string $number, string $filePath, string $mediaType, string $caption = ''): array
    {
        try {
            $token = $this->getInstanceToken($instanceName);
            $headers = [];
            if ($token) {
                $headers['Authorization'] = "Bearer {$token}";
            }

            $formData = [
                [
                    'name' => 'number',
                    'contents' => $number,
                ],
                [
                    'name' => 'mediaType',
                    'contents' => $mediaType,
                ],
                [
                    'name' => 'attachment',
                    'contents' => fopen($filePath, 'r'),
                    'filename' => basename($filePath),
                ],
            ];

            if ($caption) {
                $formData[] = ['name' => 'caption', 'contents' => $caption];
            }

            $response = $this->client->request('POST', "/message/sendMediaFile/{$instanceName}", [
                'headers' => $headers,
                'multipart' => $formData,
            ]);

            return [
                'success' => true,
                'status' => $response->getStatusCode(),
                'data' => json_decode($response->getBody()->getContents(), true),
            ];
        } catch (GuzzleException $e) {
            $response = method_exists($e, 'getResponse') ? $e->getResponse() : null;
            $data = null;

            if ($response) {
                $data = json_decode($response->getBody()->getContents(), true);
            }

            return [
                'success' => false,
                'status' => $response ? $response->getStatusCode() : 0,
                'error' => $e->getMessage(),
                'data' => $data,
            ];
        }
    }

    /**
     * Check if number is registered on WhatsApp
     */
    public function checkNumber(string $instanceName, string $number): array
    {
        return $this->request('POST', "/chat/whatsappNumbers/{$instanceName}", [
            'numbers' => [$number],
        ], $instanceName);
    }

    /**
     * Update instance settings
     */
    public function updateSettings(string $instanceName, array $settings): array
    {
        return $this->request('PUT', "/instance/settings/{$instanceName}", $settings, $instanceName);
    }

    /**
     * Make HTTP request
     */
    private function request(string $method, string $uri, array $body = [], ?string $instanceToken = null): array
    {
        try {
            $headers = [];

            if ($instanceToken) {
                $token = $this->getInstanceToken($instanceToken);
                if ($token) {
                    $headers['Authorization'] = "Bearer {$token}";
                }
            }

            $options = ['headers' => $headers];

            if ($method === 'GET') {
                $options['query'] = $body;
            } else {
                $options['json'] = $body;
            }

            $response = $this->client->request($method, $uri, $options);

            return [
                'success' => true,
                'status' => $response->getStatusCode(),
                'data' => json_decode($response->getBody()->getContents(), true),
            ];
        } catch (GuzzleException $e) {
            $response = method_exists($e, 'getResponse') ? $e->getResponse() : null;
            $data = null;

            if ($response) {
                $data = json_decode($response->getBody()->getContents(), true);
            }

            return [
                'success' => false,
                'status' => $response ? $response->getStatusCode() : 0,
                'error' => $e->getMessage(),
                'data' => $data,
            ];
        }
    }

    /**
     * Get instance token from database
     */
    private function getInstanceToken(string $instanceName): ?string
    {
        $instance = \App\Models\WhatsAppInstance::where('name', $instanceName)->first();
        return $instance?->token;
    }

    /**
     * Build vCard string
     */
    private function buildVcard(string $name, array $phones, array $emails): string
    {
        $vcard = "BEGIN:VCARD\nVERSION:3.0\nFN:{$name}\n";

        foreach ($phones as $phone) {
            $vcard .= "TEL;TYPE=CELL:{$phone}\n";
        }

        foreach ($emails as $email) {
            $vcard .= "EMAIL:{$email}\n";
        }

        $vcard .= "END:VCARD";
        return $vcard;
    }
}
