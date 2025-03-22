import scrollToElement from "./scrollToElement"

export const applySmoothScrollOnAnchorLinks = (elements: NodeListOf<HTMLLinkElement>): void => {
    elements.forEach((element) => {
        element.addEventListener("click", (event) => {
            event.preventDefault();
            const target = document.querySelector(element.getAttribute("href"));
            if (!target) return;
            scrollToElement(target as HTMLElement, { trashholdPx: window.innerHeight / 4 });
        });
    });
}