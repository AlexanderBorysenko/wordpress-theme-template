<?php
namespace ThemeCore\Services\TelegramService;

class TelegramService
{
    public function __construct(
        private string $botToken,
        private string $chatId,
    ) {
    }

    public function setChatId(string $chatId): void
    {
        $this->chatId = $chatId;
    }

    public function sendTelegramMessage($message): bool
    {
        $apiUrl = sprintf('https://api.telegram.org/bot%s/sendMessage', $this->botToken);
        $args   = [
            'body' => [
                'chat_id' => $this->chatId,
                'text'    => $message,
            ],
        ];
        try {
            $response = wp_remote_post($apiUrl, $args);
            if (is_wp_error($response)) {
                return false;
            }
            $body       = wp_remote_retrieve_body($response);
            $resultData = json_decode($body, true);
            return isset($resultData['ok']) && $resultData['ok'] === true;
        } catch (\Exception $e) {
            return false;
        }
    }

}