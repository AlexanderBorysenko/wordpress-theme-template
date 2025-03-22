const scrollToElement = (
    element: HTMLElement,
    options: {
        trashholdPx?: number,
    } = {}
) => {
    const { trashholdPx = 0 } = options;
    const offsetTop = element.getBoundingClientRect().top + window.scrollY;
    const scrollPosition = offsetTop - trashholdPx;
    window.scrollTo({
        top: scrollPosition,
        behavior: 'smooth',
    });
}

export default scrollToElement;