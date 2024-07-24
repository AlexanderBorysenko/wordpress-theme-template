export const initLinkCards = (): void => {
    const cards = document.querySelectorAll<HTMLElement>('.link-card');

    cards.forEach(card => {
        const linkItem = card.querySelector('[href]') as HTMLAnchorElement | null;
        if (linkItem) {
            const link = linkItem.href;
            card.addEventListener('click', () => {
                window.location.href = link;
            });
        }
    });
};