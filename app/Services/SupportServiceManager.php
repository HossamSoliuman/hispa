<?php

namespace App\Services;

use App\Models\SupportService;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SupportServiceManager
{
    protected ?SupportService $serviceModel = null;

    public function __construct()
    {
        // attempt to load the DB-configured service (first record)
        try {
            $this->serviceModel = SupportService::orderBy('id')->first();
        } catch (\Exception $e) {
            // DB may not be available during some console commands; ignore
            $this->serviceModel = null;
        }
    }

    /**
     * Return configured api key (decrypted) or fallback to config
     */
    public function getApiKey(): ?string
    {
        $raw = null;
        if ($this->serviceModel && $this->serviceModel->api_key) {
            $raw = $this->serviceModel->api_key;
            try {
                return decrypt($raw);
            } catch (\Exception $e) {
                // If decrypt fails, return raw value (best effort)
                return $raw;
            }
        }

        return Config::get('support_service.api_key') ?: null;
    }

    /**
     * Get endpoint (from DB or config)
     */
    public function getEndpoint(): ?string
    {
        if ($this->serviceModel && $this->serviceModel->endpoint) {
            return rtrim($this->serviceModel->endpoint, '/');
        }

        return rtrim(Config::get('support_service.endpoint', ''), '/');
    }

    /**
     * Get merged settings array
     */
    public function getSettings(): array
    {
        $defaults = Config::get('support_service', []);
        $settings = $this->serviceModel && $this->serviceModel->settings ? $this->serviceModel->settings : [];

        return array_merge($defaults, $settings ?: []);
    }

    /**
     * Send an HTTP request to the support service.
     *
     * @param  string  $method  HTTP method
     * @param  string  $path  Path relative to endpoint
     * @param  array  $options  Options: ['json' => [], 'query' => [], 'headers' => []]
     * @return \Illuminate\Http\Client\Response
     */
    public function sendRequest(string $method, string $path = '', array $options = [])
    {
        $endpoint = $this->getEndpoint();
        if (! $endpoint) {
            throw new \RuntimeException('Support service endpoint not configured.');
        }

        $url = $endpoint.'/'.ltrim($path, '/');

        $apiKey = $this->getApiKey();
        $timeout = Config::get('support_service.timeout', 10);

        $headers = $options['headers'] ?? [];

        // Default Authorization header if api key exists and no custom header provided
        if ($apiKey && empty(array_intersect(['Authorization', 'authorization', 'X-API-KEY', 'x-api-key'], array_keys($headers)))) {
            $headers['Authorization'] = 'Bearer '.$apiKey;
        }

        $client = Http::timeout($timeout)->withHeaders($headers);

        $method = strtolower($method);

        try {
            if (! empty($options['json'])) {
                $response = $client->{$method}($url, $options['json']);
            } elseif (! empty($options['query'])) {
                $response = $client->send($method, $url, ['query' => $options['query']]);
            } else {
                $response = $client->{$method}($url);
            }

            $response->throw();

            return $response;
        } catch (RequestException $e) {
            throw $e;
        }
    }

    /**
     * Lightweight connection test
     */
    public function testConnection(): bool
    {
        try {
            $resp = $this->sendRequest('get', '', []);

            return $resp->successful();
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Fetch ticket priorities from API
     * GET /api/public/priorities
     *
     * @throws \Exception
     */
    public function getPriorities(): array
    {
        try {
            $response = $this->sendRequest('get', '/api/public/priorities');
            $data = $response->json();

            // API returns: {"data": {"priorities": [...]}}
            return $data['data']['priorities'] ?? [];
        } catch (\Exception $e) {
            Log::error('Failed to fetch priorities: '.$e->getMessage());
            throw $e;
        }
    }

    /**
     * Fetch ticket categories from API
     * GET /api/public/categories
     *
     * @throws \Exception
     */
    public function getCategories(): array
    {
        try {
            $response = $this->sendRequest('get', '/api/public/categories');
            $data = $response->json();

            // API returns: {"data": {"category": {"data": [...]}}}
            return $data['data']['category']['data'] ?? [];
        } catch (\Exception $e) {
            Log::error('Failed to fetch categories: '.$e->getMessage());
            throw $e;
        }
    }

    /**
     * Create a support ticket via API
     * POST /api/public/ticket_create
     *
     * @param  array  $ticketData  Expected keys: subject, message, user_id (or name/email), priority_id, category_id
     * @return array API response data
     *
     * @throws \Exception
     */
    public function createTicket(array $ticketData): array
    {
        try {
            // Map 'message' to 'description' as required by the API
            if (isset($ticketData['message'])) {
                $ticketData['description'] = $ticketData['message'];
                unset($ticketData['message']);
            }

            $response = $this->sendRequest('post', '/api/public/ticket_create', [
                'json' => $ticketData,
                'headers' => [
                    'X-Idempotency-Key' => Str::uuid()->toString(), // Prevent duplicates on retries
                ],
            ]);

            $data = $response->json();

            // Check if response is successful based on HTTP status code
            // API may return different success indicators
            if ($response->successful()) {
                // Return the full response data
                return [
                    'is_success' => true,
                    'message' => $data['message'] ?? 'Ticket created successfully',
                    'data' => $data['data'] ?? $data,
                ];
            }

            throw new \RuntimeException($data['message'] ?? 'Failed to create ticket');
        } catch (RequestException $e) {
            $responseBody = $e->response ? $e->response->json() : [];
            Log::error('Failed to create ticket', [
                'error' => $e->getMessage(),
                'response' => $responseBody,
                'ticket_data' => $ticketData,
            ]);

            // Return validation errors if available
            if (isset($responseBody['errors'])) {
                throw new \Illuminate\Validation\ValidationException(
                    validator([], []),
                    response()->json($responseBody, 422)
                );
            }

            throw $e;
        }
    }
}
