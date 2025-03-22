export interface TFormFieldOptions<T = string> {
    value: T;
    required?: boolean;
}
export type TFormFieldStateEvent = 'valueChange' | 'errorsChange' | 'isValidChange';

export type TFormFieldValidatorFn<T> = (value: T) => boolean;

export interface TFormFieldValidator<T> {
    message: string;
    method: TFormFieldValidatorFn<T>;
}

export interface TFormFieldOptions<T = string> {
    value: T;
    required?: boolean;
}

/**
 * Класс управления состоянием поля формы.
 *
 * Поле всегда проходит валидацию (если оно обязательно или заданы валидаторы),
 * но события об ошибках (errorsChange) эмитируются только если форма была отправлена
 * (то есть, если вызван метод enableErrorMessages).
 *
 * Это позволяет, например, скрывать ошибки до первого сабмита, но затем реагировать
 * на последующие изменения значения и автоматически обновлять UI.
 *
 * @template ValueType - тип значения поля.
 */
export class FormFieldState<ValueType = string> {
    private _value: ValueType;
    private valueModifiers: Array<(value: ValueType) => ValueType> = [];
    private required: boolean;
    private validators: Array<TFormFieldValidator<ValueType>> = [];
    private _isValid: boolean = true;
    private _errors: string[] = [];
    // Флаг, указывающий, что ошибки следует выводить
    private isErrorMessagesEnabled: boolean = false;
    private events: Record<TFormFieldStateEvent, Array<(field: FormFieldState<ValueType>) => void>> = {
        valueChange: [],
        errorsChange: [],
        isValidChange: [],
    };

    constructor(options: TFormFieldOptions<ValueType>) {
        this._value = options.value;
        this.required = options.required ?? false;
        this.validate(); // первоначальная валидация
    }

    public on(event: TFormFieldStateEvent, callback: (field: FormFieldState<ValueType>) => void): void {
        this.events[event].push(callback);
    }

    public off(event: TFormFieldStateEvent, callback: (field: FormFieldState<ValueType>) => void): void {
        this.events[event] = this.events[event].filter(cb => cb !== callback);
    }

    private emit(event: TFormFieldStateEvent): void {
        this.events[event].forEach(callback => callback(this));
    }

    public setValueModifiers(modifiers: Array<(value: ValueType) => ValueType>): void {
        this.valueModifiers = modifiers;
    }

    public get value(): ValueType {
        return this._value;
    }
    public set value(newValue: ValueType) {
        // Применяем модификаторы, если они заданы
        const modifiedValue = this.valueModifiers.reduce((val, modifier) => modifier(val), newValue);
        this._value = modifiedValue;
        this.emit('valueChange');
        // Всегда пересчитываем валидность, но вывод ошибок – только если форма была отправлена
        this.validate();
    }

    public get errors(): string[] {
        return this._errors;
    }
    public set errors(newErrors: string[]) {
        this._errors = newErrors;
        // Эмитируем событие ошибок только если форма уже была отправлена
        if (this.isErrorMessagesEnabled) {
            this.emit('errorsChange');
        }
    }

    public setValidators(validators: Array<TFormFieldValidator<ValueType>>): void {
        this.validators = validators;
    }

    /**
     * Валидирует текущее значение, обновляет внутреннее состояние и эмитирует событие isValidChange.
     *
     * Всегда производится валидация, но событие об ошибках (errorsChange) срабатывает только если isErrorMessagesEnabled === true.
     *
     * @returns {boolean} true, если поле валидно, иначе false.
     */
    public validate(): boolean {
        const errors: string[] = [];

        if (!this.required) {
            return true;
        }
        if (this.required && !this.value) {
            errors.push('The field is required');
        }

        this.validators.forEach(validator => {
            if (!validator.method(this.value)) {
                errors.push(validator.message);
            }
        });

        this._isValid = errors.length === 0;
        this.errors = errors;
        this.emit('isValidChange');
        return this._isValid;
    }

    public get isValid(): boolean {
        return this._isValid;
    }

    /**
     * Метод, который нужно вызывать при сабмите формы.
     *
     * Устанавливает флаг isErrorMessagesEnabled, что приводит к эмиссии события об ошибках,
     * и позволяет UI отображать ошибки.
     *
     * @returns {boolean} true, если поле валидно, иначе false.
     */
    public enableErrorMessages(): boolean {
        this.isErrorMessagesEnabled = true;
        // Повторно вызываем validate(), чтобы эмитировать событие errorsChange с текущим состоянием
        return this.validate();
    }
}