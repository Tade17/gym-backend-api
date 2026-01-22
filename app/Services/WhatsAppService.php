<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Servicio para enviar mensajes de WhatsApp usando WAHA
 * Documentación: https://waha.devlike.pro/docs/
 */
class WhatsAppService
{
    protected string $baseUrl;
    protected string $session;
    protected ?string $apiKey;

    public function __construct()
    {
        $this->baseUrl = rtrim(env('WAHA_URL', 'http://localhost:3000'), '/');
        $this->session = env('WAHA_SESSION', 'default');
        $this->apiKey = env('WAHA_API_KEY');
    }

    /**
     * Enviar un mensaje de texto a un número de WhatsApp
     *
     * @param string $phone Número de teléfono (con código de país, ej: 51999999999)
     * @param string $message Mensaje a enviar
     * @return array
     */
    public function sendMessage(string $phone, string $message): array
    {
        try {
            // Limpiar el número (solo dígitos)
            $cleanPhone = preg_replace('/[^0-9]/', '', $phone);

            // Si el número tiene 9 dígitos, agregar código de país de Perú (51)
            // Ajusta el código de país según tu ubicación
            if (strlen($cleanPhone) === 9) {
                $cleanPhone = '51' . $cleanPhone;
            }

            // Formato para WhatsApp: número@c.us
            $chatId = "{$cleanPhone}@c.us";

            $response = Http::withHeaders($this->getHeaders())
                ->post("{$this->baseUrl}/api/sendText", [
                    'session' => $this->session,
                    'chatId' => $chatId,
                    'text' => $message
                ]);

            if ($response->successful()) {
                Log::info("WhatsApp enviado exitosamente a {$cleanPhone}");
                return [
                    'success' => true,
                    'message' => 'Mensaje enviado correctamente',
                    'data' => $response->json()
                ];
            }

            Log::error("Error enviando WhatsApp: " . $response->body());
            return [
                'success' => false,
                'message' => 'Error al enviar mensaje',
                'error' => $response->json()
            ];

        } catch (\Exception $e) {
            Log::error("Excepción enviando WhatsApp: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error de conexión con WAHA',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Enviar mensajes masivos a múltiples clientes
     *
     * @param array $clients Lista de clientes con phone_number
     * @param callable $messageBuilder Función que recibe el cliente y retorna el mensaje
     * @return array Resultados del envío
     */
    public function sendBulkMessages(array $clients, callable $messageBuilder): array
    {
        $results = [
            'total' => count($clients),
            'sent' => 0,
            'failed' => 0,
            'details' => []
        ];

        foreach ($clients as $client) {
            if (empty($client['phone_number'])) {
                $results['failed']++;
                $results['details'][] = [
                    'client_id' => $client['id'] ?? null,
                    'name' => ($client['first_name'] ?? '') . ' ' . ($client['last_name'] ?? ''),
                    'success' => false,
                    'error' => 'Sin número de teléfono'
                ];
                continue;
            }

            $message = $messageBuilder($client);
            $result = $this->sendMessage($client['phone_number'], $message);

            if ($result['success']) {
                $results['sent']++;
            } else {
                $results['failed']++;
            }

            $results['details'][] = [
                'client_id' => $client['id'] ?? null,
                'name' => ($client['first_name'] ?? '') . ' ' . ($client['last_name'] ?? ''),
                'success' => $result['success'],
                'error' => $result['error'] ?? null
            ];

            // Pequeña pausa para no saturar la API
            usleep(500000); // 0.5 segundos
        }

        return $results;
    }

    /**
     * Verificar si WAHA está conectado y la sesión está activa
     *
     * @return bool
     */
    public function isConnected(): bool
    {
        try {
            $response = Http::withHeaders($this->getHeaders())
                ->get("{$this->baseUrl}/api/sessions/{$this->session}");

            if ($response->successful()) {
                $data = $response->json();
                return ($data['status'] ?? '') === 'WORKING';
            }

            return false;
        } catch (\Exception $e) {
            Log::error("Error verificando conexión WAHA: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtener headers para las peticiones HTTP
     */
    protected function getHeaders(): array
    {
        $headers = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];

        if ($this->apiKey) {
            $headers['X-Api-Key'] = $this->apiKey;
        }

        return $headers;
    }
}
