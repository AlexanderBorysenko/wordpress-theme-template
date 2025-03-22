export default  class TableOfContents {
    private headings: HTMLElement[];
    private tableOfContents: NodeListOf<HTMLElement>;

    constructor() {
        this.headings = Array.from(
            document.querySelectorAll('h1[id], h2[id], h3[id], h4[id]')
        );
        this.tableOfContents = document.querySelectorAll<HTMLElement>('.table-of-contents');
    }

    public static init(): void {
        const instance = new TableOfContents();
        if (instance.tableOfContents.length > 0) {
            let closest: HTMLElement | null = null;

            window.addEventListener('scroll', () => {
                closest = instance.getClosestHeading(closest);
                instance.updateTableOfContents(closest);
            });
        }
    }

    private getClosestHeading(closest: HTMLElement | null): HTMLElement | null {
        this.headings.forEach(heading => {
            const bounding = heading.getBoundingClientRect();
            if (
                closest === null ||
                Math.abs(window.innerHeight / 2 - bounding.top) <
                Math.abs(window.innerHeight / 2 - closest.getBoundingClientRect().top)
            ) {
                closest = heading;
            }
        });
        return closest;
    }

    private updateTableOfContents(closest: HTMLElement | null): void {
        this.tableOfContents.forEach(toc => {
            const tocItems = Array.from(toc.querySelectorAll('.table-of-contents__item'));
            tocItems.forEach(item => item.classList.remove('_active'));

            if (closest !== null) {
                const closestTocItem = toc.querySelector<HTMLElement>(
                    `.table-of-contents__item[data-anchor="${closest.id}"]`
                );
                if (closestTocItem !== null) {
                    closestTocItem.classList.add('_active');

                    const tocContainerHeight = toc.getBoundingClientRect().height;
                    const activeItemHeight = closestTocItem.getBoundingClientRect().height;

                    toc.scrollTop = closestTocItem.offsetTop - tocContainerHeight / 2 + activeItemHeight / 2;
                }
            }
        });
    }
}
