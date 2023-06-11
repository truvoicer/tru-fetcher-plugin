import React from 'react';

const contextData = {
    settings: [],
    updateSettings: () => {},
    addSetting: () => {},
    removeSingleSetting: () => {},
    updateSingleSetting: () => {},
    createSingleSetting: () => {},
}
export default React.createContext(contextData);
