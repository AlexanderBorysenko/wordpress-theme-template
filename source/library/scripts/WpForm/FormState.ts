import { FormFieldState } from "./FormFieldState";

export type TFormStateEvents = 'submitStart' | 'submitEnd' | 'submitSuccess' | 'submitValidationError' | 'submitServerError';

export type TRegularWpFormResponce<DataType = string> = {
    success: boolean;
    data: DataType;
    redirect?: string;
    errors: {
        [key: string]: string;
    };
}

export default class FormState<ResponceType = any> {
    public fields: Map<string, FormFieldState<any>>;
    private element: HTMLFormElement;
    private events: Record<TFormStateEvents, Array<(formState: FormState) => void>> = {
        submitStart: [],
        submitEnd: [],
        submitSuccess: [],
        submitServerError: [],
        submitValidationError: [],
    };
    public errorMessage: string = '';
    private submitInProgress: boolean = false;
    private wpAjaxAction: string = '';
    public responce: TRegularWpFormResponce<ResponceType> = {
        success: false,
        data: null,
        errors: {},
    };

    constructor(element: HTMLFormElement, wpAjaxAction: string, fields: Map<string, FormFieldState<any>>) {
        this.element = element;
        this.fields = fields;
        this.wpAjaxAction = wpAjaxAction;
    }

    public initSubmitListener(options?: {
        beforeSubmit?: (event: Event) => void | Promise<void>,
        afterSubmit?: (event: Event) => void | Promise<void>
    }): void {
        this.element.addEventListener('submit', async (event) => {
            event.preventDefault();
            if (options?.beforeSubmit) await options.beforeSubmit(event);
            await this.submit();
            if (options?.afterSubmit) await options.afterSubmit(event);
        });
    }

    public on(event: TFormStateEvents, callback: (formState: FormState) => void): void {
        this.events[event].push(callback);
    }

    public off(event: TFormStateEvents, callback: (formState: FormState) => void): void {
        this.events[event] = this.events[event].filter(cb => cb !== callback);
    }

    private emit(event: TFormStateEvents): void {
        this.events[event].forEach(callback => callback(this));
    }

    public async submit(): Promise<void> {
        if (this.submitInProgress) return;

        this.emit('submitStart');

        this.submitInProgress = true;
        const formData = new FormData();

        let isFormValid = true;
        this.fields.forEach((field, key) => {
            field.enableErrorMessages();
            field.validate();
            // Support file uploads: if the value is a FileList, append each file separately.
            if (field.value instanceof FileList) {
                for (let i = 0; i < field.value.length; i++) {
                    formData.append(key, field.value.item(i)!);
                }
            } else {
                formData.append(key, field.value);
            }
            if (!field.isValid) isFormValid = false;
        });
        if (!isFormValid) {
            this.submitInProgress = false;
            this.emit('submitValidationError');
            this.emit('submitEnd');
            return;
        }

        formData.append('action', this.wpAjaxAction);
        try {
            const response = await fetch(`/wp-admin/admin-ajax.php`, {
                method: 'POST',
                body: formData,
            });
            const result: TRegularWpFormResponce<ResponceType> = await response.json();
            this.responce = result;
            if (result.success) {
                this.emit('submitSuccess');

                // Redirect if the server response contains a redirect URL.
                if (result.redirect) window.location.href = result.redirect;
            } else {
                if (result.errors) {
                    Object.entries(result.errors).forEach(([key, errorMessage]) => {
                        const field = this.fields.get(key);
                        if (field) {
                            field.errors = [errorMessage as string];
                        } else {
                            console.error('Field not found in form state:', key);
                        }
                    });
                }
                console.error('Form submission failed:', result.errors);
                this.emit('submitValidationError');
            }
        } catch (error) {
            this.errorMessage = 'An error occurred while submitting the form.';
            console.error(error);
            this.responce = {
                success: false,
                data: null,
                errors: {
                    submit: 'An error occurred while submitting the form. Please try again later.',
                },
            };
            this.emit('submitServerError');
        } finally {
            this.submitInProgress = false;
            this.emit('submitEnd');
        }
    }
}