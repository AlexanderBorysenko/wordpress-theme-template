/**
 * Creates a resize observer to update global cssheader height variable
 */
const initHeaderComponent = (): void => {
    const header = document.querySelector<HTMLElement>('.header');
    if (!header) return;

    const observer = new ResizeObserver((entries) => {
        for (let entry of entries) {
            const height = entry.contentRect.height;
            document.documentElement.style.setProperty('--header-height', `${height}px`);
        }
    });
    observer.observe(header);
}

export default initHeaderComponent;