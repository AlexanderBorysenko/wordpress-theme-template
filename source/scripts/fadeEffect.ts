/**
 * Options for the fade effect.
 */
type FadeOptions = {
    duration?: number; // Duration of the fade effect in milliseconds
    onStart?: () => void; // Callback function to be called when the fade effect starts
    onEnd?: () => void; // Callback function to be called when the fade effect ends
    display?: string; // Display property to set when the fade effect ends
};

/**
 * Fade in effect for an HTML element.
 * @param target - The HTML element to apply the fade in effect to.
 * @param options - Options for the fade effect.
 */
export const fadeIn = (target: HTMLElement, options: FadeOptions = {}): void => {
    const { duration = 300, onStart, onEnd,
        display = "block"
    } = options;

    if (onStart) {
        onStart();
    }

    const opacityStep = Math.floor(100 / (duration / 16)); // Calculate the opacity step for each interval
    let currentOpacity = 0;
    target.style.opacity = `${currentOpacity}%`;
    target.style.display = display;

    const intervalId = setInterval(() => {
        target.style.opacity = `${currentOpacity}%`;
        currentOpacity += opacityStep;

        if (currentOpacity >= 100) {
            target.style.opacity = "100%"; // Ensure the final opacity is set to 100%
            clearInterval(intervalId);
            if (onEnd) {
                onEnd();
            }
        }
    }, 16); // Update opacity every 16ms
};

/**
 * Fade out effect for an HTML element.
 * @param target - The HTML element to apply the fade out effect to.
 * @param options - Options for the fade effect.
 */
export const fadeOut = (target: HTMLElement, options: FadeOptions = {}): void => {
    const { duration = 300, onStart, onEnd } = options;

    if (onStart) {
        onStart();
    }

    const opacityStep = Math.floor(100 / (duration / 16)); // Calculate the opacity step for each interval
    let currentOpacity = 100;

    const intervalId = setInterval(() => {
        currentOpacity -= opacityStep;
        target.style.opacity = `${currentOpacity}%`;

        if (currentOpacity <= 0) {
            clearInterval(intervalId);
            target.style.display = 'none';
            if (onEnd) {
                onEnd();
            }
        }
    }, 16); // Update opacity every 16ms
};
