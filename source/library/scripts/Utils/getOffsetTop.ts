export const getOffsetTop = (elem: HTMLElement) => {
    var offsetTop = 0;
    do {
        if (!isNaN(elem.offsetTop)) {
            offsetTop += elem.offsetTop;
        }
    } while (elem = elem.offsetParent as HTMLElement);
    return offsetTop;
}