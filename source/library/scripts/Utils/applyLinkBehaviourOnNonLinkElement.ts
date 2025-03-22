export const applyLinkBehaviourOnNonLinkElement = (element: HTMLElement, options: {
    href?: string,
}): void => {
    if (!element.hasAttribute("tabindex")) {
        element.setAttribute("tabindex", "0");
    }

    const href = options.href || element.getAttribute("data-href");
    if (!href) return;

    // Add role for accessibility
    element.setAttribute("role", "link");

    // Add cursor style
    element.style.cursor = "pointer";

    // Click behavior
    element.addEventListener("click", (event) => {
        // Handle ctrl/cmd click to open in new tab
        if (event.ctrlKey || event.metaKey) {
            window.open(href, '_blank');
        } else {
            window.location.href = href;
        }
    });

    // Enter key should also trigger navigation
    element.addEventListener("keydown", (event) => {
        if (event.key === "Enter" || event.key === " ") {
            event.preventDefault();
            window.location.href = href;
        }
    });
}