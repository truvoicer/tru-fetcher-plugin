import React, {useEffect, useState} from 'react';
import SettingsContext from "./contexts/SettingsContext";
import config from "../library/api/wp/config";
import {APP_STATE} from "../library/redux/constants/app-constants";
import {SESSION_STATE} from "../library/redux/constants/session-constants";
import {connect} from "react-redux";
import {StateMiddleware} from "../library/api/StateMiddleware";

const SettingsContainer = ({children, app, session}) => {

    const stateMiddleware = new StateMiddleware();
    stateMiddleware.setAppState(app);
    stateMiddleware.setSessionState(session);
    async function fetchSettings() {
        const results = await stateMiddleware.fetchRequest({
            config: config,
            endpoint: config.endpoints.settings,
        });

        const settings = results?.data?.settings;
        if (Array.isArray(settings)) {
            settingsContextData.updateSettings(settings);
        }
    }

    async function saveSetting(setting) {
        const results = await stateMiddleware.sendRequest({
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
        const results = await stateMiddleware.sendRequest({
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
        const results = await stateMiddleware.sendRequest({
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

export default connect(
    (state) => {
        return {
            app: state[APP_STATE],
            session: state[SESSION_STATE],
        }
    },
    null
)(SettingsContainer);
