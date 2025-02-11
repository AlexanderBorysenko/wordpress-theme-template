<?php
namespace Library;

class ContactFormAjaxHandler
{
    private string $action;
    private array $fields = [];
    private bool $recaptchaEnabled = true;
    private string $recaptchaSecret = ''; // Define your default reCAPTCHA secret here.
    private string $telegramBotToken = ''; // Define your default Telegram bot token here.
    private string $telegramChatId = '';   // Define your default Telegram chat ID here.

    /**
     * Constructor.
     *
     * @param string $action The AJAX action name.
     */
    public function __construct(string $action)
    {
        $this->action = $action;
    }

    /**
     * Add a field with its validation rules.
     *
     * Example rules: ['required', 'email']
     *
     * @param string $field The field name.
     * @param array  $rules An array of rules.
     * @return self
     */
    public function addField(string $field, array $rules): self
    {
        $this->fields[$field] = $rules;
        return $this;
    }

    /**
     * Disable reCAPTCHA verification.
     *
     * @return self
     */
    public function disableRecaptcha(): self
    {
        $this->recaptchaEnabled = false;
        return $this;
    }

    /**
     * Set the reCAPTCHA credentials.
     *
     * @param string $secret The reCAPTCHA secret.
     * @return self
     */
    public function setRecaptchaCredentials(string $secret): self
    {
        $this->recaptchaSecret = $secret;
        return $this;
    }

    /**
     * Set the Telegram credentials.
     *
     * @param string $botToken The Telegram bot token.
     * @param string $chatId   The Telegram chat ID.
     * @return self
     */
    public function setTelegramCredentials(string $botToken, string $chatId): self
    {
        $this->telegramBotToken = $botToken;
        $this->telegramChatId = $chatId;
        return $this;
    }

    /**
     * Initialize the AJAX handlers.
     *
     * Registers both logged-in and not-logged-in handlers.
     *
     * @return void
     */
    public function init(): void
    {
        // Default values: if credentials not explicitly set, use get_config().
        if (empty($this->recaptchaSecret)) {
            $this->recaptchaSecret = get_config('recaptcha.secretKey', '');
        }
        if (empty($this->telegramBotToken)) {
            $this->telegramBotToken = get_config('telegram.botToken', '');
        }
        if (empty($this->telegramChatId)) {
            $this->telegramChatId = get_config('telegram.chatId', '');
        }

        add_action('wp_ajax_' . $this->action, [$this, 'handleRequest']);
        add_action('wp_ajax_nopriv_' . $this->action, [$this, 'handleRequest']);
    }

    /**
     * Main AJAX handler.
     *
     * Processes input, validates, optionally verifies reCAPTCHA,
     * sends an email and (optionally) a Telegram message,
     * and returns a JSON response.
     *
     * @return void
     */
    public function handleRequest(): void
    {
        $errors = [];
        $data = [];

        // Process and validate each registered field.
        foreach ($this->fields as $field => $rules) {
            $value = isset($_POST[$field]) ? sanitize_text_field(wp_unslash($_POST[$field])) : '';
            $data[$field] = $value;

            if (in_array('required', $rules, true) && empty($value)) {
                $errors[$field] = 'This field is required.';
            }
            if (in_array('email', $rules, true) && !is_email($value)) {
                $errors[$field] = 'Please enter a valid email address.';
            }
            if (in_array('numeric', $rules, true) && !is_numeric($value)) {
                $errors[$field] = 'Please enter a valid number.';
            }
            if (in_array('min_length', $rules, true) && strlen($value) < $rules['min_length']) {
                $errors[$field] = 'This field must be at least ' . $rules['min_length'] . ' characters long.';
            }
            if (in_array('max_length', $rules, true) && strlen($value) > $rules['max_length']) {
                $errors[$field] = 'This field must be no more than ' . $rules['max_length'] . ' characters long.';
            }
            if (in_array('regex', $rules, true) && !preg_match($rules['regex'], $value)) {
                $errors[$field] = 'This field does not match the required format.';
            }
        }

        // Optionally verify reCAPTCHA.
        if ($this->recaptchaEnabled) {
            if (!empty($_POST['g-recaptcha-response'])) {
                $recaptchaResponse = sanitize_text_field(wp_unslash($_POST['g-recaptcha-response']));
                if (!$this->verifyRecaptcha($recaptchaResponse)) {
                    $errors['recaptcha'] = 'reCAPTCHA verification failed.';
                }
            } else {
                $errors['recaptcha'] = 'reCAPTCHA response missing.';
            }
        }

        // If errors exist, return them.
        if (!empty($errors)) {
            $this->sendResponse(false, ['errors' => $errors]);
        }

        // Send email notification.
        $emailSent = $this->sendEmail($data);

        // Send Telegram message if credentials are set.
        $telegramSent = true;
        if (!empty($this->telegramBotToken) && !empty($this->telegramChatId)) {
            $telegramSent = $this->sendTelegram($data);
        }

        if ($emailSent && $telegramSent) {
            $this->sendResponse(true, 'Your message was sent successfully.');
        } else {
            $this->sendResponse(false, 'There was an error sending your message.');
        }
    }

    /**
     * Verify Google reCAPTCHA.
     *
     * @param string $responseToken The reCAPTCHA response token.
     * @return bool
     */
    private function verifyRecaptcha(string $responseToken): bool
    {
        $url = 'https://www.google.com/recaptcha/api/siteverify';
        $args = [
            'body' => [
                'secret' => $this->recaptchaSecret,
                'response' => $responseToken,
                'remoteip' => $_SERVER['REMOTE_ADDR'] ?? '',
            ],
        ];
        $result = wp_remote_post($url, $args);
        if (is_wp_error($result)) {
            return false;
        }
        $body = wp_remote_retrieve_body($result);
        $resultData = json_decode($body, true);
        return isset($resultData['success']) && $resultData['success'] === true;
    }

    /**
     * Send an email notification.
     *
     * @param array $data The sanitized form data.
     * @return bool
     */
    private function sendEmail(array $data): bool
    {
        $subject = 'New Contact Form Submission';
        $message = "You have received a new message:\n\n";
        foreach ($data as $key => $value) {
            $message .= ucfirst($key) . ': ' . $value . "\n";
        }
        // Use the admin email (change as needed).
        $to = get_option('admin_email');
        $headers = ['Content-Type: text/plain; charset=UTF-8'];
        return wp_mail($to, $subject, $message, $headers);
    }

    /**
     * Send a Telegram message.
     *
     * @param array $data The sanitized form data.
     * @return bool
     */
    private function sendTelegram(array $data): bool
    {
        $message = "New Contact Form Submission:\n\n";
        foreach ($data as $key => $value) {
            $message .= ucfirst($key) . ': ' . $value . "\n";
        }
        $apiUrl = sprintf('https://api.telegram.org/bot%s/sendMessage', $this->telegramBotToken);
        $args = [
            'body' => [
                'chat_id' => $this->telegramChatId,
                'text' => $message,
            ],
        ];
        $response = wp_remote_post($apiUrl, $args);
        if (is_wp_error($response)) {
            return false;
        }
        $body = wp_remote_retrieve_body($response);
        $resultData = json_decode($body, true);
        return isset($resultData['ok']) && $resultData['ok'] === true;
    }

    /**
     * Send a JSON response and terminate execution.
     *
     * @param bool        $success Whether the operation succeeded.
     * @param mixed       $data    Message or errors.
     * @return void
     */
    private function sendResponse(bool $success, $data): void
    {
        header('Content-Type: application/json');
        echo wp_json_encode([
            'success' => $success,
            'data' => $data,
        ]);
        wp_die();
    }
}
