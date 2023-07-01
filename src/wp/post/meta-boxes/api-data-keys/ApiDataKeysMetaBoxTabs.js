import React, {useState, useEffect} from 'react';
import {Tabs} from 'antd';
import PostMetaBoxContext from "../../contexts/PostMetaBoxContext";
import ItemDataKeysTab from "./tabs/ItemDataKeysTab";
import {APP_STATE} from "../../../../library/redux/constants/app-constants";
import {SESSION_STATE} from "../../../../library/redux/constants/session-constants";
import {connect} from "react-redux";
import Auth from "../../../../components/auth/Auth";
import {updateInitialValues, updateMetaHiddenFields} from "../helpers/metaboxes-helpers";

const initialPanes = [
    {
        name: 'data_keys',
        label: 'Data Keys',
        children: (
            <ItemDataKeysTab />
        )
    },
]

const ApiDataKeysMetaBoxTabs = ({session, config}) => {
    const [panes, setPanes] = useState([]);
    const [isInitialized, setIsInitialized] = useState(false);
    const [metaBoxContext, setMetaBoxContext] = useState({
        data: {},
        formData: {
            service: null,
            data_keys: [],
        },
        updateFormData: (key, value) => {
            setMetaBoxContext(state => {
                let cloneState = {...state};
                cloneState.formData[key] = value;
                return cloneState;
            })
        },
        updateData: (key, value) => {
            setMetaBoxContext(state => {
                let cloneState = {...state};
                cloneState.data[key] = value;
                return cloneState;
            })
        },
        updateByKey: (key, value) => {
            setMetaBoxContext(state => {
                let cloneState = {...state};
                cloneState[key] = value;
                return cloneState;
            })
        }
    });


    useEffect(() => {
        setPanes(initialPanes.map((item, index) => {
            const cloneItem = {...item};
            cloneItem.key = index;
            return cloneItem;
        }))
    }, [])

    useEffect(() => {
        if (!isInitialized) {
            return;
        }
        Object.keys(metaBoxContext.formData).forEach(field => {
            updateMetaHiddenFields({field, metaBoxContext, fieldGroupId: 'api_data_keys'});
        })
    }, [metaBoxContext])

    useEffect(() => {
        updateInitialValues({fieldGroupId: 'api_data_keys', metaBoxContext, setIsInitialized})
    }, [])

    return (
        <Auth config={config}>
            <PostMetaBoxContext.Provider value={metaBoxContext}>
                <Tabs defaultActiveKey="1" items={panes}/>
            </PostMetaBoxContext.Provider>
        </Auth>
    );
}

export default connect(
    (state) => {
        return {
            app: state[APP_STATE],
            session: state[SESSION_STATE],
        }
    },
    null
)(ApiDataKeysMetaBoxTabs);
