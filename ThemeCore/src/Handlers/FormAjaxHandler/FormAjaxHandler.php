<?php
namespace ThemeCore\Handlers\FormAjaxHandler;

use Exception;
use ThemeCore\Services\TelegramService\TelegramService;
use ThemeCore\ThemeModules\ReCaptcha\ReCaptcha;

class FormAjaxHandler
{
    private string          $action;
    private array           $fields                   = [];
    private bool            $recaptchaEnabled         = true;
    private TelegramService $telegramService;
    private array           $receiverEmails           = [];
    private                 $templateCallback         = null; /* @var callable */
    private                 $emailTemplateCallback    = null; /* @var callable */
    private                 $telegramTemplateCallback = null; /* @var callable */
    private                 $wpPostTemplateCallback   = null; /* @var callable */
    private                 $wpPostType               = 'form-orders';
    private                 $formTitle                = null;
    private                 $redirect                 = "/thank-you";
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
     * Set the Telegram credentials.
     *
     * @param string $botToken The Telegram bot token.
     * @param string $chatId   The Telegram chat ID.
     * @return self
     */
    public function setTelegramCredentials(string $botToken, string $chatId): self
    {
        $this->telegramService = new TelegramService($botToken, $chatId);
        return $this;
    }

    /**
     * Set a custom DEFAULT template callback.
     *
     * The callback receives array $data and should return a string.
     *
     * @param callable $callback
     * @return self
     */
    public function setTemplate(callable $callback): self
    {
        $this->templateCallback = $callback;
        return $this;
    }

    /**
     * Set a custom email template callback.
     *
     * The callback receives array $data and should return a string.
     *
     * @param callable $callback
     * @return self
     */
    public function setEmailTemplate(callable $callback): self
    {
        $this->emailTemplateCallback = $callback;
        return $this;
    }

    /**
     * Set a custom Telegram template callback.
     *
     * The callback receives array $data and should return a string.
     *
     * @param callable $callback
     * @return self
     */
    public function setTelegramTemplate(callable $callback): self
    {
        $this->telegramTemplateCallback = $callback;
        return $this;
    }

    /**
     * Set the receiver email(s).
     *
     * @param string|array $emails The receiver email(s).
     * @return self
     */
    public function setReceiverEmails(array $emails): self
    {
        $this->receiverEmails = $emails;
        return $this;
    }

    /**
     * Set a custom WP post template callback.
     *
     * The callback receives array $data and should return a string.
     *
     * @param callable $callback
     * @return self
     */
    public function setWpPostTemplate(callable $callback): self
    {
        $this->wpPostTemplateCallback = $callback;
        return $this;
    }

    /**
     * Set the WP post type.
     *
     * @param string $postType The WP post type.
     * @return self
     */
    public function setWpPostType(string $postType): self
    {
        $this->wpPostType = $postType;
        return $this;
    }

    /**
     * Set the WP post title callback.
     *
     * The callback receives array $data and should return a string.
     *
     * @param callable $callback
     * @return self
     */
    public function setFormTitle(callable $callback): self
    {
        $this->formTitle = function (array $data) use ($callback) {
            return $callback($data);
        };
        return $this;
    }

    /**
     * Set the redirect you page URL.
     *
     * @param string $url The thank you page URL.
     * @return self
     */
    public function setRedirect(string $url): self
    {
        $this->redirect = $url;
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
        if (empty($this->telegramBotToken)) {
            $this->telegramBotToken = getThemeСonfig('telegram.botToken', '');
        }
        if (empty($this->telegramChatId)) {
            $this->telegramChatId = getThemeСonfig('telegram.chatId', '');
        }
        if (empty($this->templateCallback)) {
            $this->templateCallback = function (array $data) {
                $message = "You have received a new message:\n\n";
                foreach ($data as $key => $value) {
                    if (is_array($value)) {
                        foreach ($value as $item) {
                            if ($item['type'] === 'attachment') {
                                $message .= "<a href='{$item['url']}'>{$item['file_name']}</a>\n";
                            }
                        }
                    } else {
                        $message .= ucfirst($key) . ': ' . $value . "\n";
                    }
                }
                return $message;
            };
        }
        registerAjaxAction($this->action, [
            $this,
            'handleRequest'
        ]);
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
        $data   = [];
        // Temporary storage for files to upload after validations.
        $filesToUpload = [];

        // Process and validate each registered field.
        foreach ($this->fields as $field => $rules) {
            // Check if the field is a file upload.
            if (isset($_FILES[$field])) {
                $file = $_FILES[$field];

                // Check if a file is required.
                if (in_array('required', $rules, true) && $file['error'] === UPLOAD_ERR_NO_FILE) {
                    $errors[$field] = 'This file field is required.';
                    continue;
                }

                // If a file is uploaded.
                if ($file['error'] === UPLOAD_ERR_OK) {
                    // Validate max size if provided (in MB).
                    if (isset($rules['max_size'])) {
                        $maxBytes = $rules['max_size'] * 1024 * 1024;
                        if ($file['size'] > $maxBytes) {
                            $errors[$field] = 'The file exceeds the maximum allowed size of ' . $rules['max_size'] . ' MB.';
                            continue;
                        }
                    }
                    // Validate file type: if rule includes 'image', check if it's a valid image.
                    if (in_array('image', $rules, true)) {
                        $check = getimagesize($file['tmp_name']);
                        if ($check === false) {
                            $errors[$field] = 'Uploaded file is not a valid image.';
                            continue;
                        }
                    }
                    // Additional file rules can be added here if needed.

                    // Save the file for later upload if all validations pass.
                    $filesToUpload[$field] = $file;
                } else {
                    // If some error occurred other than no file.
                    if ($file['error'] !== UPLOAD_ERR_NO_FILE) {
                        $errors[$field] = 'File upload error code: ' . $file['error'];
                    }
                }
            } else {
                // Process non-file fields.
                $value        = isset($_POST[$field]) ? sanitize_text_field(wp_unslash($_POST[$field])) : '';
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
                if (in_array('tel', $rules, true)) {
                    $onlyDigits = preg_replace('/\D/', '', $value);
                    if (strlen($onlyDigits) < 10) {
                        $errors[$field] = 'Please enter a valid phone number.';
                    }
                }
                if (isset($rules['min_length']) && strlen($value) < $rules['min_length']) {
                    $errors[$field] = 'This field must be at least ' . $rules['min_length'] . ' characters long.';
                }
                if (isset($rules['max_length']) && strlen($value) > $rules['max_length']) {
                    $errors[$field] = 'This field must be no more than ' . $rules['max_length'] . ' characters long.';
                }
                if (isset($rules['regex']) && !preg_match($rules['regex'], $value)) {
                    $errors[$field] = 'This field does not match the required format.';
                }
            }
        }

        // If no errors so far, process the file uploads.
        if (empty($errors)) {
            foreach ($filesToUpload as $field => $file) {
                $mediaFileId    = storeUploadInMediaGallery($file);
                $data[$field][] = [
                    'type'          => 'attachment',
                    'url'           => wp_get_attachment_url($mediaFileId),
                    'file_name'     => basename($file['name']),
                    'attachment_id' => $mediaFileId,
                ];
            }
        }

        // Optionally verify reCAPTCHA.
        if ($this->recaptchaEnabled && ReCaptcha::getInstance()->getSecretKey()) {
            if (!empty($_POST['g-recaptcha-response'])) {
                $recaptchaResponse = sanitize_text_field(wp_unslash($_POST['g-recaptcha-response']));
                if (!ReCaptcha::getInstance()->verify($recaptchaResponse)['success']) {
                    $errors['recaptcha'] = 'reCAPTCHA verification failed.';
                }
            } else {
                $errors['recaptcha'] = 'reCAPTCHA response missing.';
            }
        }

        // If errors exist, return them.
        if (!empty($errors)) {
            $this->sendResponse(false, null, $errors);
        }

        // Send email notification.
        $emailSent = $this->sendEmail($data);

        // Send Telegram message if credentials are set.
        $telegramSent = true;
        if (!empty($this->telegramBotToken) && !empty($this->telegramChatId)) {
            $telegramSent = $this->telegramService->sendTelegramMessage($data);
        }

        // Create a WP post with the submitted data.
        $wpPostCreated = $this->createWpPost($data);

        if ($emailSent && $telegramSent && $wpPostCreated) {
            $this->sendResponse(true, 'Your message was sent successfully.');
        } else {
            $this->sendResponse(false, 'There was an error sending your message.');
        }
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
        if (is_callable($this->formTitle)) {
            $subject = call_user_func($this->formTitle, $data);
        }

        $message = is_callable($this->emailTemplateCallback)
            ? call_user_func($this->emailTemplateCallback, $data)
            : call_user_func($this->templateCallback, $data);

        foreach ($this->receiverEmails ?? [] as $email) {
            $to      = $email;
            $headers = ['Content-Type: text/html; charset=UTF-8'];
            wp_mail($to, $subject, $message, $headers);
        }

        return true;
    }

    /**
     * Create a form-orders post with the submitted data.
     * 
     * @param array $data The sanitized form data.
     * @return bool 
     */
    private function createWpPost(array $data): bool
    {
        $postContent = is_callable($this->wpPostTemplateCallback)
            ? call_user_func($this->wpPostTemplateCallback, $data)
            : call_user_func($this->templateCallback, $data);

        $postTitle = 'New Form Submit';
        if (is_callable($this->formTitle)) {
            $postTitle = call_user_func($this->formTitle, $data);
        }
        $post = [
            'post_title'   => $postTitle,
            'post_content' => $postContent,
            'post_status'  => 'publish',
            'post_type'    => $this->wpPostType,
        ];

        $postId = wp_insert_post($post);
        return $postId !== 0;
    }

    /**
     * Output JSON response and terminate execution.
     *
     * @param array $payload The JSON response payload.
     * @return void
     */
    private function outputResponse(array $payload): void
    {
        header('Content-Type: application/json');
        echo wp_json_encode($payload);
        wp_die();
    }

    /**
     * Send a JSON response.
     *
     * @param bool  $success Whether the operation succeeded.
     * @param mixed $data    Message or additional data.
     * @param array $errors  Optional errors array.
     * @return void
     */
    private function sendResponse(bool $success, $data, array $errors = []): void
    {
        $response = [
            'success' => $success,
            'data'    => $data,
        ];

        if ($success)
            $response['redirect'] = $this->redirect;


        if (!empty($errors))
            $response['errors'] = $errors;


        $this->outputResponse($response);
    }

}
