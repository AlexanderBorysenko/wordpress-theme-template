import { FormFieldState } from "./FormFieldState";

export abstract class AbstractFormField<T = string> {
    protected element: HTMLElement;
    public State: FormFieldState<T>;

    constructor(element: HTMLElement) {
        this.element = element;
        this.State = this.createFieldState(element);
        this.init();
    }

    protected abstract createFieldState(element: HTMLElement): FormFieldState<T>;
    protected abstract init(): void;
}