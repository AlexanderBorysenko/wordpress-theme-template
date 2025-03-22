type Callback = () => void;

type ModalWindow = {
    name: string;
    onOpen: Callback;
    onClose: Callback;
};

type TActionOptions = {
    onOpen?: Callback;
    onClose?: Callback;
};

export function useModalStore() {
    const modalStore = new Map<string, ModalWindow>();
    let currentModalWindow: string | undefined;
    const modalOpenCallbacks: Array<(modalName: string) => void> = [];
    const modalCloseCallbacks: Array<(modalName: string) => void> = [];

    const registerModalWindow = (name: string, { onOpen = () => { }, onClose = () => { } }: TActionOptions = {}): void => {
        if (modalStore.has(name)) {
            console.error(`Modal window with name "${name}" already exists.`);
            return;
        }
        modalStore.set(name, { name, onOpen, onClose });
    };

    const closeModalWindow = (name?: string, callback: Callback = () => { }): void => {
        const modalName = name || currentModalWindow;
        if (!modalName) return;

        const modal = modalStore.get(modalName);
        if (!modal) {
            console.error(`Modal window with name "${modalName}" not found.`);
            return;
        }

        modal.onClose();
        callback();
        currentModalWindow = undefined;
        modalCloseCallbacks.forEach(cb => cb(modalName));
    };

    const openModalWindow = (name: string, callback: Callback = () => { }): void => {
        const modal = modalStore.get(name);
        if (!modal) {
            console.error(`Modal window with name "${name}" not found.`);
            return;
        }

        if (currentModalWindow === name) {
            console.warn(`Modal window with name "${name}" is already open.`);
            return;
        }

        if (currentModalWindow) {
            closeModalWindow(currentModalWindow);
        }

        modal.onOpen();
        callback();
        currentModalWindow = name;
        modalOpenCallbacks.forEach(cb => cb(name));
    };

    const toggleModalWindow = (name: string, { onOpen = () => { }, onClose = () => { } }: TActionOptions = {}): void => {
        currentModalWindow === name
            ? closeModalWindow(name, onClose)
            : openModalWindow(name, onOpen);
    };

    const setCurrentModalWindow = (name: string, callback: Callback = () => { }): void => {
        if (currentModalWindow) {
            closeModalWindow(currentModalWindow);
        }

        const modal = modalStore.get(name);
        if (!modal) {
            currentModalWindow = undefined;
            return;
        }

        modal.onOpen();
        currentModalWindow = name;
        callback();
    };

    return {
        registerModalWindow,
        closeModalWindow,
        openModalWindow,
        toggleModalWindow,
        setCurrentModalWindow,
        onModalOpen: (callback: (modalName: string) => void) => modalOpenCallbacks.push(callback),
        onModalClose: (callback: (modalName: string) => void) => modalCloseCallbacks.push(callback),
        getCurrentModalWindow: () => currentModalWindow,
        isModalOpen: (name: string) => currentModalWindow === name,
    };
}
