// return processed masked value for phone input in format +1 (999) 999-99-99
export const phoneMaskValue = (value: string): string => {
    const countryCode = '+1 ';
    if (!value) {
        return "";
    }
    value = value.replace(/^\+1/, '');
    let x = value.replace(/\D/g, '').match(/(\d{0,3})(\d{0,3})(\d{0,4})/);
    value = !x[2] ? x[1] : '(' + x[1] + ') ' + x[2] + (x[3] ? '-' + x[3] : '');
    return countryCode + value;
}
