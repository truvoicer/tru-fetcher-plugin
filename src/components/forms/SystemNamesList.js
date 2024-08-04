import React from 'react';
import {useEffect, useState} from '@wordpress/element';
import {TreeSelect} from '@wordpress/components';
import config from "../../library/api/wp/config";
import {StateMiddleware} from "../../library/api/StateMiddleware";
import {isObject} from "../../library/helpers/utils-helpers";

function SystemNamesList(props) {
    const [systemNames, setSystemNames] = useState(null);
    const [ selectedName, setSelectedName ] = useState( null );

    const stateMiddleware = new StateMiddleware();
    stateMiddleware.setAppState(props?.reducers?.app);
    stateMiddleware.setSessionState(props?.reducers?.session);

    function fetchSystemNames() {
        stateMiddleware.fetchRequest({
            config: config,
            endpoint: `${config.endpoints.system}/names`,
        }).then((results) => {
            if (isObject(results?.data?.data)) {
                setSystemNames(results.data.data);
            }
        })
    }
    function buildTree() {
        if (!isObject(systemNames)) {
            return [];
        }
        return Object.keys(systemNames).map((key) => {
            return {
                name: key,
                id: key,
                children: systemNames[key].map((name) => {
                    return {
                        name: name,
                        id: `${key}-${name}`,
                    }
                }),
            }
        });
    }

    function getSelectedNameData() {
        if (!isObject(systemNames)) {
            return null;
        }
        for (const key of buildTree()) {
            for (const name of key.children) {
                if (name.id === selectedName) {
                    return {
                        system: key.id,
                        name: name.name,
                    }
                }
            }
        }
        return null;
    }
    useEffect(() => {
        fetchSystemNames();
    }, []);

    useEffect(() => {
        if (selectedName) {
            props.onChange(getSelectedNameData()?.name);
        }
    }, [selectedName]);

    return (
        <TreeSelect
            __nextHasNoMarginBottom
            label="Select a system name"
            // noOptionLabel="No parent page"
            onChange={ ( newPage ) => setSelectedName( newPage ) }
            selectedId={ selectedName }
            tree={buildTree()}
        />
    );
}

export default SystemNamesList;
