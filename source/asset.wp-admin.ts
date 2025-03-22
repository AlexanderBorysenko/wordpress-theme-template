window.addEventListener('DOMContentLoaded', () => {
    const setMinHeight = () => {
        const previews = document.querySelectorAll<HTMLElement>('.cf-block__preview');
        previews.forEach((preview) => {
            if (preview.querySelector('.components-placeholder')) return;
            const contentHeight = preview.querySelector('div')?.scrollHeight || 0;
            const block = preview.closest<HTMLElement>('.wp-block');
            if (block) {
                block.style.minHeight = `${contentHeight}px`;
            }
        });
    };

    // Run once on load
    setMinHeight();

    // Use MutationObserver to respond to DOM changes instead of intervals
    const observer = new MutationObserver((mutations) => {
        // You can fine-tune which mutations should trigger which handlers
        let shouldUpdate = false;
        mutations.forEach((mutation) => {
            if (mutation.type === 'childList') {
                shouldUpdate = true;
            }
        });
        if (shouldUpdate) {
            setMinHeight();
        }
    });

    // Observe changes in the entire document body. Adjust subtree if needed.
    observer.observe(document.body, { childList: true, subtree: true });
});