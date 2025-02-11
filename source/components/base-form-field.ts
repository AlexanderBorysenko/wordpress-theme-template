import { validateEmail, validatePhone, validateText } from "../scripts/validations";

const baseFormFieldSelector = ".base-form-field"

export const setFieldHasError = (field: HTMLElement, hasError: boolean) => {
    if (hasError) field.classList.add('_has-error');
    else field.classList.remove('_has-error');
};

export const setFieldIsFocused = (field: HTMLElement, isFocused: boolean) => {
    if (isFocused) field.classList.add('_focus');
    else field.classList.remove('_focus');
}
export const setFieldValidity = (field: HTMLElement, isValid: boolean) => {
    if (isValid) { field.classList.add('_valid'); setFieldHasError(field, false); }
    else field.classList.remove('_valid');
};

export const validateField = (field: HTMLElement) => {
    const validationType = field.dataset.validation;

    if (!validationType) return;
    let isValid = false;
    const valueControl = field.querySelector<HTMLInputElement>('[name]');
    if (!valueControl) return;
    const value = valueControl.value;
    if (validationType === 'email') {
        isValid = validateEmail(value);
    } else if (validationType === 'phone') {
        isValid = validatePhone(value);
    } else if (validationType === 'text') {
        isValid = validateText(value);
    }
    setFieldValidity(field, isValid);
};

export const focusBaseFormField = () => {
    const baseFormFields = document.querySelectorAll<HTMLElement>(".base-form-field");

    baseFormFields.forEach((baseFormField: HTMLElement) => {
        const interactiveElements = baseFormField.querySelectorAll<HTMLElement>(`[name]`);

        interactiveElements.forEach((interactiveElement: HTMLElement) => {
            interactiveElement.addEventListener('focus', () => {
                setFieldIsFocused(baseFormField, true);
            });

            interactiveElement.addEventListener('blur', () => {
                setFieldIsFocused(baseFormField, false);
            });

            interactiveElement.addEventListener('input', () => {
                setFieldHasError(baseFormField, false);
            });
        })
    })
};

export const handleFieldError = (name: string, error: string, formElement: HTMLFormElement) => {
    const fieldControlElement = formElement.querySelector(`${baseFormFieldSelector} [name="${name}"]`);
    const field = fieldControlElement?.closest<HTMLElement>(baseFormFieldSelector);

    if (!field) return;

    setFieldHasError(field, true);

    const errorMessageElement = field.querySelector<HTMLElement>(`${baseFormFieldSelector}__error`);

    if (errorMessageElement)
        errorMessageElement.textContent = error;
}