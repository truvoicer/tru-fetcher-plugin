import React, {useState, useEffect} from 'react';
import {Tabs} from 'antd';
import ItemGeneralTab from "./components/comparisons/ItemGeneralTab";
import PostMetaBoxContext from "./contexts/PostMetaBoxContext";
import ItemDataKeysTab from "./components/comparisons/ItemDataKeysTab";
import {APP_STATE} from "../../library/redux/constants/app-constants";
import {SESSION_STATE} from "../../library/redux/constants/session-constants";
import {connect} from "react-redux";
import Auth from "../../components/auth/Auth";
import ItemCustomTab from "./components/comparisons/ItemCustomTab";

const PostMetaBoxTabs = ({session}) => {
    const [panes, setPanes] = useState([]);
    const [metaBoxContext, setMetaBoxContext] = useState({
        data: {
            type: 'api_data_keys',
            data_keys: [],
            custom: {
                item_image: null,
                item_header: null,
                item_text: null,
                item_rating: null,
                item_link_text: null,
                item_link: null,
                item_badge_text: null,
                item_badge_link: null,
            },
        },
        updateData: (key, value) => {
            setMetaBoxContext(state => {
                let cloneState = {...state};
                cloneState.data[key] = value;
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
            console.log({metaBoxContext})
            switch (metaBoxContext.data.type) {
                case 'custom':
                    data = updatePanes({
                        insertPanes: [{
                            name: 'custom',
                            label: 'Custom',
                            children: <ItemCustomTab />,
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

    function updateMetaHiddenFields(field) {
        const fieldName = `trf_post_meta_${field}`;
        const hiddenField = document.querySelector(`input[name="${fieldName}"]`);
        const data = metaBoxContext.data[field];
        if (typeof data === 'object' || Array.isArray(data)) {
            hiddenField.value = JSON.stringify(data);
        } else {
            hiddenField.value = data;
        }
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
        Object.keys(metaBoxContext.data).forEach(field => {
            updateMetaHiddenFields(field);
        })
    }, [metaBoxContext])

    return (
        <Auth config={tru_fetcher_react?.api?.tru_fetcher}>
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
)(PostMetaBoxTabs);
