const instances: HTMLElement[] = [];
const blockSelector = '.wp-block[data-type="carbon-fields/content-block"]';

class ContentBlockWpAdmin {
    private readonly instance: HTMLElement;

    marginSelect: HTMLSelectElement;
    marginValue: string = '';
    marginPrevValue: string = '';

    orientationSelect: HTMLSelectElement;
    orientationValue: string = '';
    orientationPrevValue: string = '';

    constructor(block: HTMLElement) {
        if (instances.includes(block)) return;

        this.instance = block;
        this.marginSelect = this.instance.querySelector('[name="margin_bottom"]');
        this.marginValue = this.marginSelect?.value || '';
        this.marginPrevValue = this.marginValue;
        this.orientationSelect = this.instance.querySelector('[name="orientation"]');
        this.orientationValue = this.orientationSelect?.value || '';
        this.orientationPrevValue = this.orientationValue;

        this.init();
    }

    public init(): void {
        this.marginSelect?.addEventListener('change', this.onMarginChange);
        this.orientationSelect?.addEventListener('change', this.onOrientationChange);

        this.handleBlockClasses();

        const observer = new MutationObserver((mutationsList) => {
            for (const mutation of mutationsList) {
                if (mutation.type === 'attributes') {
                    if (mutation.attributeName === 'class') {
                        this.handleBlockClasses();
                    }
                }
            }
        });

        observer.observe(this.instance, { attributes: true });
    }

    private handleBlockClasses(): void {
        if (!this.instance.classList.contains(this.marginValue)) {
            if (this.marginPrevValue) this.instance.classList.remove(this.marginPrevValue);
            if (this.marginValue) this.instance.classList.add(this.marginValue);
        }
        if (!this.instance.classList.contains(this.orientationValue)) {
            if (this.orientationPrevValue) this.instance.classList.remove(this.orientationPrevValue);
            if (this.orientationValue) this.instance.classList.add(this.orientationValue);
        }
    }

    private onMarginChange = (): void => {
        this.marginPrevValue = this.marginValue;
        this.marginValue = this.marginSelect.value || '';
        this.handleBlockClasses()
    }

    private onOrientationChange = (): void => {
        this.orientationPrevValue = this.orientationValue;
        this.orientationValue = this.orientationSelect.value || '';
        this.handleBlockClasses()
    }
}

export const initContentBlocksWpAdmin = (): void => {
    const blocks = document.querySelectorAll<HTMLElement>(blockSelector);
    blocks.forEach((block) => {
        new ContentBlockWpAdmin(block);
    });
    setTimeout(() => {
        initContentBlocksWpAdmin();
    }, 500);
}