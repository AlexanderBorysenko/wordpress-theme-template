import { phoneMaskValue } from "../scripts/phoneMask";
import { handleFieldError, setFieldHasError, setFieldIsFocused, validateField } from "./base-form-field";


export const initContactForm = () => {
    const contactForms = document.querySelectorAll<HTMLFormElement>('.base-contact-form');

    const fields = document.querySelectorAll<HTMLElement>('.form-field');
    fields.forEach((field) => {
        const fieldInteractiveElement = field.querySelector<HTMLInputElement>('[name]');

        if (fieldInteractiveElement?.getAttribute('type') === 'tel') {
            fieldInteractiveElement.addEventListener('input', () => {
                fieldInteractiveElement.value = phoneMaskValue(fieldInteractiveElement.value);
            });
        }
        fieldInteractiveElement?.addEventListener('focus', () => {
            setFieldIsFocused(field, true);
        });
        fieldInteractiveElement?.addEventListener('blur', () => {
            setFieldIsFocused(field, false);
        });
        fieldInteractiveElement?.addEventListener('input', () => {
            validateField(field);
        });
        fieldInteractiveElement?.addEventListener('invalid', () => {
            setFieldHasError(field, true);
        });
    });

    contactForms.forEach((contactForm) => {
        const toggleFormIsProcessing = () => {
            contactForm.classList.toggle("_processing")
        }

        const formAction = contactForm.getAttribute('action');
        const handleErrors = (errors: { [key: string]: string }) => {
            Object.keys(errors).forEach((fieldName) => {
                handleFieldError(fieldName, errors[fieldName], contactForm);
            })
        }
        contactForm.addEventListener('submit', (e) => {
            e.preventDefault();

            toggleFormIsProcessing();

            const formData = new FormData(contactForm);

            fetch(`/wp-admin/admin-ajax.php?action=${formAction}`, {
                method: 'POST',
                body: formData,
            })
                .then((data) => {
                    return data.json();
                })
                .then((response) => {
                    if (response.success) {
                        try {
                            //@ts-ignore
                            dataLayer.push({ 'event': 'form_submit' });
                        } catch (e) {
                            console.error(e);
                        }
                        window.location = response.data.redirect;
                    } else {
                        handleErrors(response.data.errors)
                    }
                })
                .catch((error) => {
                    alert(error)
                })
                .finally(() => {
                    toggleFormIsProcessing();
                })
        });
    })
};