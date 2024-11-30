class PageAnchors {
    private static instance: PageAnchors;
    private anchors: HTMLAnchorElement[] = [];

    private constructor() {
        this.collectAnchors();
        this.addClickListeners();
    }

    public static init(): PageAnchors {
        if (!PageAnchors.instance) {
            PageAnchors.instance = new PageAnchors();
        }
        return PageAnchors.instance;
    }

    private collectAnchors(): void {
        this.anchors = Array.from(document.querySelectorAll('a[href^="#"]'));
    }

    private addClickListeners(): void {
        this.anchors.forEach(anchor => {
            const targetId = anchor.getAttribute('href')!.substring(1);
            const targetElement = document.getElementById(targetId);

            if (targetElement) {
                anchor.addEventListener('click', (event) => {
                    event.preventDefault();
                    this.scrollToElement(targetElement);
                });
            }
        });
    }

    private scrollToElement(element: HTMLElement): void {
        const targetPosition = element.getBoundingClientRect().top + window.scrollY - (window.innerHeight * 0.4);
        window.scrollTo({
            top: targetPosition,
            behavior: 'smooth'
        });
    }
}

export default PageAnchors;