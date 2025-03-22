import camelToKebabCase from "../Utils/camelToKebabCase";

type SingletonConstructor<T> = {
    new(element: HTMLElement): T;
    componentName: string;
    _instance?: T;
};

export default abstract class SingletonComponent {
    protected element: HTMLElement;
    public static _instance?: SingletonComponent;

    static get componentName(): string {
        return camelToKebabCase(this.name);
    }

    constructor(element: HTMLElement) {
        this.element = element;
        this.constructDependencies();
    }

    protected abstract constructDependencies(): void;
    protected abstract init(): void;

    /**
     * Статичний метод для отримання сінглтон-інстансу компонента.
     * Якщо інстанс уже створено, він повертається.
     * Інакше здійснюється пошук елемента у документі за id, що відповідає componentName.
     * Якщо елемент знайдено – створюється новий інстанс, викликається init() і зберігається.
     */
    static getInstance<T extends SingletonComponent>(
        this: SingletonConstructor<T>
    ): T {
        if (this._instance) {
            return this._instance as T;
        }

        const element = document.getElementById(this.componentName);
        if (!element) {
            console.error(`Element with id "${this.componentName}" not found for singleton component "${this.name}"`);
            return null;
        }

        const instance = new this(element);
        instance.init();
        this._instance = instance;
        return instance;
    }
}