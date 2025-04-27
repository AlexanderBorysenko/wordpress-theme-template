export const allowTransitions = (className = 'allow-transitions') => {
    const html = document.querySelector('html');
    if (html) {
        html.classList.add(className);
    }
}