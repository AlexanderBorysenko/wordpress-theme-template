<?php
namespace ThemeCore\ThemeModules\ReCaptcha;

use ThemeCore\ThemeModules\ThemeModule;

/**
 * ReCaptchaHandler handler class (Singleton)
 */
class ReCaptcha extends ThemeModule
{
    private $siteKey;
    private $secretKey;

    /**
     * Private constructor to prevent direct instantiation
     */
    protected function __construct(array $config)
    {
        $this->siteKey   = $config['siteKey'];
        $this->secretKey = $config['secretKey'];
    }

    public function init()
    {
        /**
         * Inject reCAPTCHA frontend scripts
         */
        add_action('wp_footer', function () {
            echo $this->getFrontendScripts();
        });
    }

    public function getSiteKey()
    {
        return $this->siteKey;
    }

    public function getSecretKey()
    {
        return $this->secretKey;
    }

    /**
     * Get the frontend scripts for reCAPTCHA
     * 
     * @return string|void HTML output for reCAPTCHA scripts
     */
    public function getFrontendScripts()
    {
        if (!$this->siteKey)
            return;
        ob_start();
        ?>
        <script>
            (function ()
            {
                // Ensure the recaptcha API script is added only once.
                function loadRecaptchaScript(callbackName)
                {
                    if (!document.querySelector('script[src^="https://www.google.com/recaptcha/api.js"]'))
                    {
                        var script = document.createElement('script');
                        // Use explicit render mode with an onload callback.
                        script.src = 'https://www.google.com/recaptcha/api.js?onload=' + callbackName + '&render=explicit';
                        script.defer = true;
                        script.async = true;
                        document.head.appendChild(script);
                    }
                }

                // Create a hidden container for the reCAPTCHA widget.
                var containerId = 'recaptcha-container';
                var recaptchaContainer = document.getElementById(containerId);
                if (!recaptchaContainer)
                {
                    recaptchaContainer = document.createElement('div');
                    recaptchaContainer.id = containerId;
                    recaptchaContainer.style.display = 'none';
                    document.body.appendChild(recaptchaContainer);
                }

                // This variable will store the reCAPTCHA widget ID.
                var widgetId = null;

                // This function is called when the reCAPTCHA API has loaded.
                window.onRecaptchaApiLoad = function ()
                {
                    widgetId = grecaptcha.render(recaptchaContainer, {
                        'sitekey': '<?= $this->siteKey ?>',
                        'size': 'invisible'
                    });
                };

                // Load the reCAPTCHA script with the specified callback.
                loadRecaptchaScript('onRecaptchaApiLoad');

                /**
                 * Retrieves a reCAPTCHA token by executing the already rendered widget.
                 * This function returns a Promise that resolves with the token.
                 * It re-uses the same widget for repeated calls.
                 *
                 * Usage:
                 *    const token = await window.getRecaptchaResult();
                 */
                window.getRecaptchaResult = async function ()
                {
                    return new Promise(function (resolve, reject)
                    {
                        if (typeof grecaptcha === 'undefined')
                        {
                            return reject(new Error('reCAPTCHA not loaded'));
                        }
                        // Wait for widgetId if not yet rendered.
                        if (widgetId === null)
                        {
                            var interval = setInterval(function ()
                            {
                                if (widgetId !== null)
                                {
                                    clearInterval(interval);
                                    executeRecaptcha();
                                }
                            }, 100);
                        } else
                        {
                            executeRecaptcha();
                        }
                        // Execute the widget with fresh callbacks.
                        function executeRecaptcha()
                        {
                            try
                            {
                                grecaptcha.execute(widgetId, {
                                    'callback': function (token)
                                    {
                                        resolve(token);
                                    },
                                    'error-callback': function ()
                                    {
                                        reject(new Error('reCAPTCHA error'));
                                    }
                                });
                            } catch (e)
                            {
                                reject(e);
                            }
                        }
                    });
                };
            })();
        </script>
        <style>
            /* Ensure the recaptcha container is hidden */
            #recaptcha-container {
                display: none;
            }
        </style>
        <?php
        return ob_get_clean();
    }

    public function verify($recaptchaResponse)
    {
        if (!$this->secretKey || !$recaptchaResponse) {
            return [
                'success' => false,
                'error'   => 'Missing reCAPTCHA secret key or response'
            ];
        }

        $url  = 'https://www.google.com/recaptcha/api/siteverify';
        $data = [
            'secret'   => $this->secretKey,
            'response' => $recaptchaResponse,
            'remoteip' => $_SERVER['REMOTE_ADDR'] ?? ''
        ];

        $options = [
            'http' => [
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'content' => http_build_query($data)
            ]
        ];

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);

        if ($response === false) {
            return [
                'success' => false,
                'error'   => 'Failed to connect to reCAPTCHA server'
            ];
        }

        $result = json_decode($response, true);

        return $result['success'] ? true : [
            'success' => false,
            'error'   => $result['error-codes'] ?? 'Unknown error'
        ];
    }

}