import React, {useEffect, useState} from 'react';
import SettingsContext from "./contexts/SettingsContext";
import {fetchRequest, sendRequest} from "../library/api/state-middleware";
import config from "../library/api/wp/config";

const SettingsContainer = ({children}) => {

    async function fetchSettings() {
        const results = await fetchRequest({
            config: config,
            endpoint: config.endpoints.settings,
        });

        const settings = results?.data?.settings;
        if (Array.isArray(settings)) {
            settingsContextData.updateSettings(settings);
        }
    }

    async function saveSetting(setting) {
        const results = await sendRequest({
            config: config,
            method: 'post',
            endpoint: `${config.endpoints.settings}/create`,
            data: setting
        });

        const settings = results?.data?.settings;
        if (Array.isArray(settings)) {
            settingsContextData.updateSettings(settings);
        }
    }

    async function createSetting(setting) {
        const results = await sendRequest({
            config: config,
            method: 'post',
            endpoint: `${config.endpoints.settings}/create`,
            data: setting
        });

        const settings = results?.data?.settings;
        if (Array.isArray(settings)) {
            settingsContextData.updateSettings(settings);
        }
    }
    async function updateSetting(setting) {
        const results = await sendRequest({
            config: config,
            method: 'post',
            endpoint: `${config.endpoints.settings}/${setting.id}/update`,
            data: setting
        });

        const settings = results?.data?.settings;
        if (Array.isArray(settings)) {
            settingsContextData.updateSettings(settings);
        }
    }
    useEffect(() => {
        fetchSettings();
    }, []);

    const [settingsContextData, setSettingsContextData] = useState({
        settings: [],
        updateSettings: (settings) => {
            setSettingsContextData((prevState) => {
                let cloneSettingsContext = {...prevState};
                cloneSettingsContext.settings = settings;
                return cloneSettingsContext;
            });
        },
        addSetting: (setting) => {
            saveSetting(setting);
            // setSettingsContextData((prevState) => {
            //     let cloneSettingsContext = {...prevState};
            //     let cloneSettings = [...cloneSettingsContext.settings];
            //     cloneSettings.push({
            //         name: setting.name,
            //         value: setting.value,
            //     });
            //     cloneSettingsContext.settings = cloneSettings;
            //     return cloneSettingsContext;
            // });
        },
        removeSingleSetting: (index) => {
            setSettingsContextData((prevState) => {
                let cloneSettingsContext = {...prevState};
                let cloneSettings = [...cloneSettingsContext.settings];
                if (typeof cloneSettings[index] !== 'undefined') {
                    cloneSettings.splice(index, 1);
                }
                cloneSettingsContext.settings = cloneSettings;
                return cloneSettingsContext;
            });
        },
        updateSingleSetting: updateSingleSetting,
        createSingleSetting: createSingleSetting,
    });
    function updateSingleSetting(setting) {
        updateSetting(setting);
    }
    function createSingleSetting(setting) {
        createSetting(setting);
    }

    return (
        <SettingsContext.Provider value={settingsContextData}>
            {children}
        </SettingsContext.Provider>
    );
};

export default SettingsContainer;
