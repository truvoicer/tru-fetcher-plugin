import React from 'react';

const contextData = {
    settings: [],
    updateSettings: () => {},
    addSetting: () => {},
    removeSettingByIndex: () => {},
    updateSettingByIndex: () => {},
}
export default React.createContext(contextData);
