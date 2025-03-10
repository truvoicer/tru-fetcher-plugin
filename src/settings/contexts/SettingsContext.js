import React from 'react';

const contextData = {
    settings: [],
    refresh: () => {},
    updateSettings: () => {},
    addSetting: () => {},
    removeSingleSetting: () => {},
    updateSingleSetting: () => {},
    createSingleSetting: () => {},
}
export default React.createContext(contextData);
