/**
 * Composable function to detect clicks outside a specific element
 */
export function useOutsideClickListener() {
    type OutsideClickHandler = {
        callback: (e: Event) => void;
        removeAfterFirstTrigger?: boolean;
        eventHandler: (e: MouseEvent) => void;
    };

    const listenersStore = new Map<HTMLElement, OutsideClickHandler>();

    /**
     * Add a click outside listener to an element
     * @param element Element to watch for outside clicks
     * @param callback Function to execute when click happens outside
     * @param once Whether to remove the listener after the first trigger
     */
    const addListener = (element: HTMLElement, callback: (e: Event) => void, params: {
        removeAfterFirstTrigger?: boolean;
    } = {}): void => {
        if (!element) return;

        // Prevent duplicate listeners on the same element
        removeListener(element);

        const eventHandler = (e: MouseEvent) => {
            const target = e.target as Node;
            if (!element.contains(target) && target !== element) {
                callback(e);
                if (params.removeAfterFirstTrigger) {
                    removeListener(element);
                }
            }
        };

        listenersStore.set(element, {
            callback,
            removeAfterFirstTrigger: params.removeAfterFirstTrigger,
            eventHandler
        });

        // Use requestAnimationFrame instead of setTimeout for better performance
        requestAnimationFrame(() => {
            document.addEventListener('click', eventHandler);
        });
    };

    /**
     * Remove a previously added click outside listener
     * @param element Element to remove the listener from
     */
    const removeListener = (element: HTMLElement): void => {
        if (!element) return;

        const listener = listenersStore.get(element);
        if (listener) {
            document.removeEventListener('click', listener.eventHandler);
            listenersStore.delete(element);
        }
    };

    return {
        addListener,
        removeListener,
    };
}