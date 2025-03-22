/**
 * Handles form submission with a POST request.
 * Supports `onError` and `onSuccess` callbacks.
 * 
 * @param event The submit event from the form.
 * @param callbacks Callbacks for handling success and error responses.
 * @returns void
 */
export const handleFormSubmit = async (
    event: SubmitEvent,
    callbacks: {
        onSuccess?: (data: any) => void;
        onError?: (errors: Record<string, string>) => void;
    }
): Promise<void> => {
    event.preventDefault();

    const form = event.target as HTMLFormElement;

    if (!form || !form.action) {
        callbacks.onError?.({ general: "Form or action URL not found." });
        return;
    }

    const formData = new FormData(form);

    try {
        const response = await fetch(form.action, {
            method: "POST",
            body: formData,
        });

        const result = await response.json();

        if (!response.ok || !result.success) {
            // Handle validation or server-side errors
            if (result.data) {
                callbacks.onError?.(result.data);
            } else {
                callbacks.onError?.({ general: result.message || "Form submission failed." });
            }
        } else {
            callbacks.onSuccess?.(result.data);
        }
    } catch (error) {
        // Handle unexpected errors
        callbacks.onError?.({ general: "An unexpected error occurred. Please try again later." });
    }
};