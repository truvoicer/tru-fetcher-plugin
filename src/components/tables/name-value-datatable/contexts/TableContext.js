export const tableContextData = {
    modal: {
        show: false,
        title: '',
        component: null,
        footer: null,
        width: 520,
        onCancel: () => {
        },
        onOk: () => {
        }
    },
    renderModal: () => {},
    closeModal: () => {},
}
export default React.createContext(tableContextData);
