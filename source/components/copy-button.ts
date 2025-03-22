import Component from "~/library/scripts/Components/Component";

export default class CopyButton extends Component {
    protected get componentName(): string {
        return 'copy-button';
    }

    protected constructDependencies(): void {
    }

    public init(): void {
        this.element.addEventListener('click', this.copyToClipboard.bind(this));
    }

    private copyToClipboard(): void {
        const url = window.location.href;

        navigator.clipboard.writeText(url)
            .then(() => {
                console.log('URL copied to clipboard!');
                this.showNotification();
            })
            .catch(err => {
                console.error('Failed to copy URL: ', err);
            });
    }

    private showNotification(): void {
        this.element.classList.add('_copied');

        setTimeout(() => {
            this.element.classList.remove('_copied');
        }, 3000);
    }
}
