declare global {
    interface Window {
        /**
         * Retrieves a reCAPTCHA token by executing the rendered widget.
         * Returns a Promise that resolves to a string token.
         */
        getRecaptchaResult: () => Promise<string>;
        onRecaptchaApiLoad: () => void;
    }
}
export { };
