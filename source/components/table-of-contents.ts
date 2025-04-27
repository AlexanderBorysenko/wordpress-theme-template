import Component from "~/library/scripts/Components/Component";

export default class TableOfContents extends Component {
    private headings: HTMLElement[];
    private closestHeading: HTMLElement;
    private tocList: HTMLElement;

    protected constructDependencies(): void {
        // Get all headings with IDs
        this.headings = Array.from(
            document.querySelectorAll('h1[id], h2[id], h3[id], h4[id]')
        ) as HTMLElement[];

        // Get the list inside table of contents
        this.tocList = this.element.querySelector<HTMLElement>('.table-of-contents__list');
    }

    public init(): void {
        // Only proceed if we have headings
        if (this.headings.length === 0) return;

        // Initialize closest heading
        this.updateClosestHeading();
        this.updateActiveItem();

        // Add scroll event listener
        window.addEventListener('scroll', this.handleScroll.bind(this));
    }

    private handleScroll(): void {
        this.updateClosestHeading();
        this.updateActiveItem();
    }

    private updateClosestHeading(): void {
        let closest: HTMLElement | null = null;

        this.headings.forEach((heading: HTMLElement) => {
            const bounding = heading.getBoundingClientRect();
            if (
                closest === null ||
                Math.abs(window.innerHeight / 2 - bounding.top) <
                Math.abs(
                    window.innerHeight / 2 -
                    closest.getBoundingClientRect().top
                )
            ) {
                closest = heading;
            }
        });

        console.log('Closest heading:', closest);

        this.closestHeading = closest;
    }

    private updateActiveItem(): void {
        // Remove active class from all items
        const tocItems = Array.from(
            this.element.querySelectorAll('.table-of-contents__item')
        );
        tocItems.forEach(item => item.classList.remove('_active'));

        // Add active class to closest item
        if (this.closestHeading !== null) {
            const closestTocItem = this.element.querySelector<HTMLElement>(
                `.table-of-contents__item[data-anchor="${this.closestHeading.id}"]`
            );

            if (closestTocItem !== null) {
                closestTocItem.classList.add('_active');

                // Handle scroll following if enabled
                const tocContainerHeight = this.element.getBoundingClientRect().height;
                const activeItemHeight = closestTocItem.getBoundingClientRect().height;

                const offsetY = (closestTocItem.offsetTop - tocContainerHeight / 2 + activeItemHeight / 2) * -1;

                if (offsetY < 0 &&
                    Math.abs(offsetY) + tocContainerHeight <= this.tocList.getBoundingClientRect().height) {
                    this.tocList.style.transform = `translateY(${offsetY}px)`;
                }
            }
        }
    }
}