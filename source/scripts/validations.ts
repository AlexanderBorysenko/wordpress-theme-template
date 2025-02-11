export const validateEmail = (value: string) => {
    const emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
    return emailPattern.test(value);
};
export const validatePhone = (value: string) => {
    const phonePattern = /^\+1 \(\d{3}\) \d{3}-\d{4}$/;
    return phonePattern.test(value);
};
export const validateText = (value: string) => {
    return value?.length > 0;
}