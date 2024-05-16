import '../styles/wp-admin.scss';

window.addEventListener('DOMContentLoaded', () => {
    const setMinHeight = () => {
        const previews = document.querySelectorAll<HTMLElement>('.cf-block__preview');
        previews.forEach((preview) => {
            if (preview.querySelector('.components-placeholder')) return;
            const contentHeight = preview.querySelector('div')?.scrollHeight || 0;
            preview.style.minHeight = `${contentHeight}px`;
        });
    };

    setInterval(setMinHeight, 500);
});