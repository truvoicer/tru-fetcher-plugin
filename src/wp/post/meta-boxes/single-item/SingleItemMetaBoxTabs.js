import React, {useState, useEffect} from 'react';
import {Tabs} from 'antd';
import ItemGeneralTab from "./tabs/ItemGeneralTab";
import PostMetaBoxContext from "../../contexts/PostMetaBoxContext";
import ItemDataKeysTab from "./tabs/ItemDataKeysTab";
import {APP_STATE} from "../../../../library/redux/constants/app-constants";
import {SESSION_STATE} from "../../../../library/redux/constants/session-constants";
import {connect} from "react-redux";
import Auth from "../../../../components/auth/Auth";
import CustomItemFormFields from "../../components/item/CustomItemFormFields";
import {updateInitialValues, updateMetaHiddenFields} from "../helpers/metaboxes-helpers";

const SingleItemMetaBoxTabs = ({session, config}) => {
    const [panes, setPanes] = useState([]);
    const [isInitialized, setIsInitialized] = useState(false);
    const [metaBoxContext, setMetaBoxContext] = useState({
        data: {
            type: 'api_data_keys',
            data_keys: [],
            item_image: null,
            item_header: null,
            item_text: null,
            item_rating: null,
            item_link_text: null,
            item_link: null,
            item_badge_text: null,
            item_badge_link: null,
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

    const initialPanes = [
        {

            name: 'general',
            label: 'General',
            children: (
                <ItemGeneralTab
                    onChange={(data) => {
                        console.log({data})
                    }}
                />
            )
        },
    ]

    function updatePanes({insertPanes = [], removePanes = [], panes = []}) {
        if (insertPanes.length > 0) {
            insertPanes.forEach(insertPane => {
                const findIndex = panes.find(item => item.name === insertPane.name);
                if (!findIndex) {
                    panes.push(insertPane);
                }
            })
        }
        if (removePanes.length > 0) {
            removePanes.forEach(removePane => {
                const findIndex = panes.findIndex(item => item.name === removePane);
                if (findIndex !== -1) {
                    panes.splice(findIndex, 1);
                }
            })
        }
        return panes;
    }

    function updateTabsByType() {
        setPanes(paneState => {
            let cloneState = [...paneState];
            let data = [];
            switch (metaBoxContext.data.type) {
                case 'custom':
                    data = updatePanes({
                        insertPanes: [{
                            name: 'custom',
                            label: 'Custom',
                            children: <CustomItemFormFields
                                onChange={({value, item, index}) => {
                                    metaBoxContext.updateData(
                                        item.name,
                                        value,
                                    )
                                }}
                            />,
                        }],
                        removePanes: ['data_keys'],
                        panes: cloneState,
                    });
                    break;
                case 'api_data_keys':
                default:
                    data = updatePanes({
                        insertPanes: [{
                            name: 'data_keys',
                            label: 'Data Keys',
                            children: (
                                <ItemDataKeysTab />
                            )
                        }],
                        removePanes: ['custom'],
                        panes: cloneState,
                    });
                    break;
            }
            return data.map((item, index) => {
                const cloneItem = {...item};
                cloneItem.key = index;
                return cloneItem;
            });
        })
    }


    useEffect(() => {
        setPanes(initialPanes.map((item, index) => {
            const cloneItem = {...item};
            cloneItem.key = index;
            return cloneItem;
        }))
    }, [])

    useEffect(() => {
        updateTabsByType();
        if (!isInitialized) {
            return;
        }
        Object.keys(metaBoxContext.data).forEach(field => {
            updateMetaHiddenFields({field, metaBoxContext, fieldGroupId: 'single_item'});
        })
    }, [metaBoxContext])

    useEffect(() => {
        updateTabsByType();
        updateInitialValues({fieldGroupId: 'single_item', metaBoxContext, setIsInitialized})
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
)(SingleItemMetaBoxTabs);
