type ModalWindow = {
    name: string;
    onOpen: () => void;
    onClose: () => void;
};

type TActionOptions = {
    onOpen?: () => void;
    onClose?: () => void;
}

class ModalStore {
    private modalWindows: ModalWindow[] = [];
    private currentModalWindow?: string;

    public registerModalWindow(name: string, options: TActionOptions) {
        const { onOpen = () => { }, onClose = () => { } } = options;

        if (this.modalWindows.find((modal) => modal.name === name)) {
            console.error(`Modal window with name "${name}" already exists.`);
            return;
        }

        this.modalWindows.push({ name, onOpen, onClose });
    }

    public closeModalWindow(name: string, callback = () => { }) {
        const modal = this.modalWindows.find((modal) => modal.name === name);
        if (!modal) {
            console.error(`Modal window with name "${name}" not found.`);
            return;
        }

        modal.onClose();
        callback();
        this.currentModalWindow = undefined;
    }

    public openModalWindow(name: string, callback = () => { }) {
        const modal = this.modalWindows.find((modal) => modal.name === name);
        if (!modal) {
            console.error(`Modal window with name "${name}" not found.`);
            return;
        }

        if (this.currentModalWindow === name) {
            console.warn(`Modal window with name "${name}" is already open.`);
            return;
        }

        if (this.currentModalWindow) {
            this.closeModalWindow(this.currentModalWindow); // Close any previously opened modal
        }

        modal.onOpen();
        callback();
        this.currentModalWindow = name;
    }

    public toggleModalWindow(name: string, options: TActionOptions) {
        const { onOpen = () => { }, onClose = () => { } } = options;

        if (this.currentModalWindow === name) {
            this.closeModalWindow(name,
                onClose,
            );
        } else {
            this.openModalWindow(name,
                onOpen,
            );
        }
    }

    public setCurrentModalWindow(name: string, callback: () => void) {
        if (this.currentModalWindow) {
            this.closeModalWindow(this.currentModalWindow); // Close any previously opened modal
        }

        const modal = this.modalWindows.find((modal) => modal.name === name);
        if (!modal) {
            // close any previously opened modal
            this.currentModalWindow = undefined;

            return;
        }

        modal.onOpen();
        this.currentModalWindow = name;
        callback();
    }
}

const store = new ModalStore();

export const registerModalWindow = (name: string,
    options: TActionOptions) => store.registerModalWindow(name, options);

export const setCurrentModalWindow = (
    name: string,
    callback = () => { },
) => store.setCurrentModalWindow(name, callback);

export const toggleModalWindow = (name: string, options: TActionOptions) =>
    store.toggleModalWindow(name, options);
