import camelToKebabCase from "../Utils/camelToKebabCase";

export default abstract class Component {
    protected element: HTMLElement;

    static get componentName(): string {
        return camelToKebabCase(this.name);
    }

    constructor(element: HTMLElement) {
        this.element = element;

        this.constructDependencies();
    }

    protected abstract constructDependencies(): void;
    abstract init(): void;

    static initAll<T extends Component>(
        this: { new(element: HTMLElement): T, componentName: string },
    ): void {
        if (!this.componentName) {
            this.componentName = camelToKebabCase(this.name);
        }

        document
            .querySelectorAll<HTMLElement>(`[data-component="${this.componentName}"]`)
            .forEach(element => {
                new this(element).init();
            });
    }
}