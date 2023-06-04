import React, {useState, useEffect} from 'react';
import {Tab} from 'semantic-ui-react'
import ItemGeneralTab from "./components/comparisons/ItemGeneralTab";
import PostMetaBoxContext from "./contexts/PostMetaBoxContext";
import ItemDataKeysTab from "./components/comparisons/ItemDataKeysTab";
import PostMetaBox from "./PostMetaBox";
import {APP_STATE} from "../../library/redux/constants/app-constants";
import {SESSION_STATE} from "../../library/redux/constants/session-constants";
import {connect} from "react-redux";
import Auth from "../../components/auth/Auth";

const PostMetaBoxTabs = ({session}) => {
    const [panes, setPanes] = useState([]);
    const [metaBoxContext, setMetaBoxContext] = useState({
        data: {
            type: 'api_data_keys',
            data_keys: [],
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
            menuItem: 'General',
            render: (a) => {
                return (
                    <Tab.Pane attached={false}>
                        <ItemGeneralTab
                            onChange={(data) => {
                                console.log({data})
                            }}
                        />
                    </Tab.Pane>
                )
            }
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

    useEffect(() => {
        setPanes(initialPanes)
    }, [])
    useEffect(() => {
        setPanes(paneState => {
            let cloneState = [...paneState];
            switch (metaBoxContext.data.type) {
                case 'custom':
                    return updatePanes({
                        insertPanes: [{
                            name: 'custom',
                            menuItem: 'Custom',
                            render: () => <Tab.Pane attached={false}>Custom</Tab.Pane>,
                        }],
                        removePanes: ['data_keys'],
                        panes: cloneState,
                    });
                case 'api_data_keys':
                default:
                    return updatePanes({
                        insertPanes: [{
                            name: 'data_keys',
                            menuItem: 'Data Keys',
                            render: (a) => {
                                return (
                                    <Tab.Pane attached={false}>
                                        <ItemDataKeysTab
                                        />
                                    </Tab.Pane>
                                )
                            }
                        }],
                        removePanes: ['custom'],
                        panes: cloneState,
                    });
            }
        })
    }, [metaBoxContext])
    console.log({session})
    return (
        <Auth config={tru_fetcher_react?.api?.tru_fetcher}>
            <PostMetaBoxContext.Provider value={metaBoxContext}>
                <Tab
                    menu={{secondary: true, pointing: true}}
                    panes={panes}
                    onTabChange={(e, data) => {
                        console.log({e, data})

                        // setPanes(initialPanes)
                    }}
                />
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
