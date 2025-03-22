import { AbstractFormField } from "~/scripts/library/FormCore/AbstractFormField";
import { phoneModifier } from "~/scripts/library/FormCore/formFieldModifiers";
import { FormFieldState } from "~/scripts/library/FormCore/FormFieldState";
import { emailValidator, phoneValidator } from "~/scripts/library/FormCore/formFieldValidators";

export default class FormField extends AbstractFormField<string | string[] | FileList | null> {
    private formControl?: HTMLInputElement;
    private errorMessageElement!: HTMLElement;

    protected createFieldState(
        element: HTMLElement
    ): FormFieldState<string> | FormFieldState<string[]> | FormFieldState<FileList | null> {
        if (!element) {
            console.error("Элемент не найден", this);
            throw new Error("Элемент не найден");
        }

        const inputControls = this.getInputControls(element);
        const primaryControl = inputControls[0];
        this.errorMessageElement = this.getErrorMessageElement(element);

        if (primaryControl.type === "file") {
            return this.createFileFieldState(primaryControl);
        }

        // Если тип checkbox и найдено несколько input, работаем в режиме группы
        if (primaryControl.type === "checkbox") {
            if (inputControls.length > 1) {
                const initialValue = this.getInitialValue(primaryControl, inputControls) as string[];
                const fieldState = new FormFieldState<string[]>({
                    value: initialValue,
                    required: primaryControl.hasAttribute("required"),
                });
                this.bindValidityAndErrorHandlers(fieldState);
                this.bindMultipleCheckboxEvents(primaryControl, inputControls, fieldState);
                return fieldState;
            } else {
                const initialValue = this.getInitialValue(primaryControl, inputControls) as string;
                const fieldState = new FormFieldState<string>({
                    value: initialValue,
                    required: primaryControl.hasAttribute("required"),
                });
                this.bindValidityAndErrorHandlers(fieldState);
                this.bindInputGroupEvents(primaryControl, inputControls, fieldState);
                return fieldState;
            }
        }

        // Для radio и остальных полей — значение строка
        const initialValue = this.getInitialValue(primaryControl, inputControls) as string;
        const fieldState = new FormFieldState<string>({
            value: initialValue,
            required: primaryControl.hasAttribute("required"),
        });
        this.bindValidityAndErrorHandlers(fieldState);

        if (primaryControl.type === "radio") {
            this.bindInputGroupEvents(primaryControl, inputControls, fieldState);
        } else {
            this.bindStandardInputEvents(primaryControl, fieldState);
        }

        return fieldState;
    }

    private createFileFieldState(
        control: HTMLInputElement
    ): FormFieldState<FileList | null> {
        const initialValue = control.files || null;
        const fieldState = new FormFieldState<FileList | null>({
            value: initialValue,
            required: control.hasAttribute("required"),
        });
        control.addEventListener("change", () => {
            fieldState.value = control.files;
        });
        this.bindValidityAndErrorHandlers(fieldState);
        return fieldState;
    }

    protected getInputControls(element: HTMLElement): HTMLInputElement[] {
        const controls = Array.from(element.querySelectorAll<HTMLInputElement>("[name]"));
        if (controls.length === 0) {
            throw new Error("Контрол формы не найден");
        }
        return controls;
    }

    private getErrorMessageElement(element: HTMLElement): HTMLElement {
        const errorEl = element.querySelector<HTMLElement>("[class*='__error-message']");
        if (!errorEl) {
            throw new Error("Элемент для сообщения об ошибке не найден");
        }
        return errorEl;
    }

    /**
     * Возвращает начальное значение поля.
     * Для radio – строка (значение выбранного элемента или пустая строка).
     * Для группы чекбоксов – массив выбранных значений.
     * Для одиночного чекбокса – строка "true" или "false".
     * Для остальных – значение input.value.
     */
    private getInitialValue(control: HTMLInputElement, controls: HTMLInputElement[]): string | string[] {
        switch (control.type) {
            case "radio": {
                const checkedRadio = controls.find(input => input.checked);
                return checkedRadio ? checkedRadio.value : "";
            }
            case "checkbox": {
                if (controls.length > 1) {
                    return controls.filter(input => input.checked).map(input => input.value);
                }
                return control.checked ? "true" : "false";
            }
            default: {
                return control.value;
            }
        }
    }

    private bindValidityAndErrorHandlers<T>(fieldState: FormFieldState<T>): void {
        fieldState.on("isValidChange", () => {
            this.element.classList.toggle("_valid", fieldState.isValid);
        });

        fieldState.on("errorsChange", () => {
            this.errorMessageElement.textContent = fieldState.errors.join(", ");
            this.element.classList.toggle("_has-error", fieldState.errors.length > 0);
        });
    }

    /**
     * Обработка событий для радио и одиночного чекбокса.
     */
    private bindInputGroupEvents(
        primaryControl: HTMLInputElement,
        controls: HTMLInputElement[],
        fieldState: FormFieldState<string>
    ): void {
        controls.forEach(input => {
            input.addEventListener("change", () => {
                if (primaryControl.type === "radio") {
                    const checkedRadio = controls.find(el => el.checked);
                    fieldState.value = checkedRadio ? checkedRadio.value : "";
                    return;
                }
                if (primaryControl.type === "checkbox") {
                    fieldState.value = input.checked ? "true" : "false";
                }
            });
        });

        fieldState.on("valueChange", () => {
            if (primaryControl.type === "radio") {
                controls.forEach(input => input.checked = input.value === fieldState.value);
            } else if (primaryControl.type === "checkbox") {
                primaryControl.checked = fieldState.value === "true";
            }
        });
    }

    /**
     * Обработка событий для группы чекбоксов (множественный выбор).
     */
    private bindMultipleCheckboxEvents(
        primaryControl: HTMLInputElement,
        controls: HTMLInputElement[],
        fieldState: FormFieldState<string[]>
    ): void {
        controls.forEach(input => {
            input.addEventListener("change", () => {
                const checkedValues = controls.filter(el => el.checked).map(el => el.value);
                fieldState.value = checkedValues;
            });
        });

        fieldState.on("valueChange", () => {
            controls.forEach(input => {
                input.checked = fieldState.value.includes(input.value);
            });
        });
    }

    private bindStandardInputEvents(control: HTMLInputElement, fieldState: FormFieldState<string>): void {
        this.formControl = control;
        this.formControl.addEventListener("input", () => {
            fieldState.value = this.formControl!.value;
        });
        fieldState.on("valueChange", () => {
            this.formControl!.value = fieldState.value;
        });
    }

    /**
     * Если несколько элементов с одинаковым data-form-field-name найдены,
     * агрегируем их input-элементы.
     */
    public static createFormFieldState(
        parent: HTMLElement,
        name: string
    ): FormFieldState<string | string[] | FileList | null> {
        const fieldElements = Array.from(parent.querySelectorAll(`[data-form-field-name="${name}"]`)) as HTMLElement[];
        if (fieldElements.length === 0) {
            throw new Error(`Элементы с data-form-field-name="${name}" не найдены`);
        }

        // Если найден один элемент – работаем как обычно
        const mainElement = fieldElements[0];

        const formField = new FormField(mainElement);

        // Если группа элементов (например, несколько чекбоксов), переопределяем getInputControls
        if (fieldElements.length > 1) {
            formField.getInputControls = function (): HTMLInputElement[] {
                let inputs: HTMLInputElement[] = [];
                fieldElements.forEach(el => {
                    inputs = inputs.concat(Array.from(el.querySelectorAll("[name]")));
                });
                if (inputs.length === 0) {
                    throw new Error("Контрол формы не найден");
                }
                return inputs;
            }
            // Для errorMessageElement используем первый найденный (либо можно объединить их, если нужно)
            formField.errorMessageElement = formField.getErrorMessageElement(mainElement);
        }

        // Определяем тип поля по первому найденному input
        const input = mainElement.querySelector<HTMLInputElement>("[type]");
        const inputType = input?.getAttribute("type") || "text";
        switch (inputType) {
            case "email":
                formField.State.setValidators([emailValidator]);
                break;
            case "tel":
                formField.State.setValueModifiers([phoneModifier]);
                formField.State.setValidators([phoneValidator]);
                break;
            // Для остальных типов логика уже реализована в createFieldState.
            default:
                break;
        }

        return formField.State;
    }

    public static gatherFormFields(parent: HTMLElement): Map<string, FormFieldState<string | string[] | FileList | null>> {
        const fields = new Map<string, FormFieldState<string | string[] | FileList | null>>();
        parent.querySelectorAll<HTMLElement>("[data-form-field-name]").forEach(element => {
            const name = element.getAttribute("data-form-field-name");
            if (!name) {
                throw new Error("Атрибут data-form-field-name не задан");
            }
            fields.set(name, FormField.createFormFieldState(parent, name));
        });
        return fields;
    }

    public init(): void {
        // Дополнительная инициализация при необходимости
    }
}