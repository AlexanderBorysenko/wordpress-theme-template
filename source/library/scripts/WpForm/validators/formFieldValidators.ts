import { TFormFieldValidator } from "../FormFieldState";

export const emailValidator: TFormFieldValidator<string> = {
    message: 'Email is invalid',
    method: (value: string) => /^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/i.test(value),
};

export const phoneValidator: TFormFieldValidator<string> = {
    message: 'Phone is invalid',
    method: (value: string) => value.replace(/\D/g, '').length >= 10,
};
