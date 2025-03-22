interface OverflowGrabScrollOptions {
    orientation?: 'horizontal' | 'vertical';
    ignoreGrabOn?: string[];
}

interface OverflowGrabScrollResult {
    cleanup: () => void;
}

/**
 * A composable that adds grab-to-scroll functionality to an HTML element.
 * This allows users to click and drag to scroll content horizontally or vertically,
 * similar to how touch scrolling works on mobile devices.
 * 
 * @interface OverflowGrabScrollOptions
 * @property {('horizontal'|'vertical')} [orientation='horizontal'] - Direction of scrolling
 * @property {string[]} [ignoreGrabOn=[]] - Array of CSS selectors for elements that should not trigger grab-scroll
 * 
 * @interface OverflowGrabScrollResult
 * @property {() => void} cleanup - Function to remove all event listeners when no longer needed
 * 
 * @param {HTMLElement} element - The DOM element to add grab-to-scroll behavior to
 * @param {OverflowGrabScrollOptions} [options={}] - Configuration options
 * @returns {OverflowGrabScrollResult} Object containing cleanup function
 * 
 * @example
 * // Basic usage with default options (horizontal scrolling)
 * const scrollContainer = document.querySelector('.scroll-container');
 * const { cleanup } = useOverflowGrabScroll(scrollContainer);
 * 
 * // Clean up when no longer needed (e.g., component unmount)
 * cleanup();
 * 
 * @example
 * // Vertical scrolling with elements to ignore
 * const verticalScroller = document.querySelector('.vertical-scroller');
 * const { cleanup } = useOverflowGrabScroll(verticalScroller, {
 *   orientation: 'vertical',
 *   ignoreGrabOn: ['.clickable-button', '.drag-handle']
 * });
 * 
 * @example
 * // Usage with Vue.js ref
 * const containerRef = ref<HTMLElement | null>(null);
 * 
 * onMounted(() => {
 *   if (containerRef.value) {
 *     const { cleanup } = useOverflowGrabScroll(containerRef.value);
 *     
 *     // Clean up when component is unmounted
 *     onBeforeUnmount(cleanup);
 *   }
 * });
 */
const useOverflowGrabScroll = (element: HTMLElement, options: OverflowGrabScrollOptions = {}): OverflowGrabScrollResult => {
    const { orientation = 'horizontal', ignoreGrabOn = [] } = options;

    let isGrabing = false;
    let startPos = 0;
    let scrollPos = 0;
    let isSingleClick = false;
    let clickTimeout: number | null = null;
    let currentEvent: MouseEvent | TouchEvent | null = null;

    const getEventPos = (e: MouseEvent | TouchEvent) => {
        if (e instanceof MouseEvent) {
            return orientation === 'horizontal' ? e.clientX : e.clientY;
        } else {
            return orientation === 'horizontal' ? e.touches[0].clientX : e.touches[0].clientY;
        }
    };

    const setGrabingStyles = (isGrabing: boolean) => {
        isGrabing ? element.classList.add('grabbing') : element.classList.remove('grabbing');
        document.body.style.cursor = isGrabing ? 'grabbing' : '';
        element.style.userSelect = isGrabing ? 'none' : '';
    }

    const shouldIgnoreEvent = (e: Event) => {
        return ignoreGrabOn.some(selector => (e.target as HTMLElement).closest(selector));
    }

    const onStart = (e: MouseEvent | TouchEvent) => {
        if (shouldIgnoreEvent(e)) return;
        if (((e as MouseEvent).button !== 0)
            || ((e as TouchEvent).touches && (e as TouchEvent).touches.length > 1)
        ) return;
        isSingleClick = true;
        currentEvent = e;
        e.preventDefault();
        isGrabing = true;
        startPos = getEventPos(e);
        scrollPos = orientation === 'horizontal' ? element.scrollLeft : element.scrollTop;
        setTimeout(() => {
            setGrabingStyles(true);
        }, 100);
    }

    const onMove = (e: MouseEvent | TouchEvent) => {
        isSingleClick = false;
        if (!isGrabing) return;
        const currentPos = getEventPos(e);
        const diff = currentPos - startPos;
        if (orientation === 'horizontal') {
            element.scrollLeft = scrollPos - diff;
        } else {
            element.scrollTop = scrollPos - diff;
        }
    }

    const onEnd = () => {
        if (isSingleClick && currentEvent) {
            (currentEvent.target as HTMLElement).click();
        }
        isGrabing = false;
        setGrabingStyles(false);
        if (clickTimeout !== null) {
            clearTimeout(clickTimeout);
            clickTimeout = null;
        }
    }

    const onMouseDown = (e: MouseEvent) => onStart(e);
    const onMouseMove = (e: MouseEvent) => onMove(e);
    const onMouseUp = () => onEnd();
    const onTouchStart = (e: TouchEvent) => onStart(e);
    const onTouchMove = (e: TouchEvent) => onMove(e);
    const onTouchEnd = () => onEnd();

    // Set up event listeners
    element.addEventListener('mousedown', onMouseDown);
    document.addEventListener('mousemove', onMouseMove);
    document.addEventListener('mouseup', onMouseUp);
    element.addEventListener('touchstart', onTouchStart);
    document.addEventListener('touchmove', onTouchMove);
    document.addEventListener('touchend', onTouchEnd);

    // Return cleanup function
    const cleanup = () => {
        element.removeEventListener('mousedown', onMouseDown);
        document.removeEventListener('mousemove', onMouseMove);
        document.removeEventListener('mouseup', onMouseUp);
        element.removeEventListener('touchstart', onTouchStart);
        document.removeEventListener('touchmove', onTouchMove);
        document.removeEventListener('touchend', onTouchEnd);
    };

    return { cleanup };
};

export default useOverflowGrabScroll;