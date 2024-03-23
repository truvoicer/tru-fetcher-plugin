import React, {useState, useEffect} from 'react';
import {Modal, Card, Select, Space, Tabs, Row, Col, Button} from 'antd';
import PostMetaBoxContext from "../../contexts/PostMetaBoxContext";
import {APP_STATE} from "../../../../library/redux/constants/app-constants";
import {SESSION_STATE} from "../../../../library/redux/constants/session-constants";
import {connect} from "react-redux";
import Auth from "../../../../components/auth/Auth";
import {findMetaBoxConfig, updateInitialValues, updateMetaHiddenFields} from "../helpers/metaboxes-helpers";
import {fetchRequest} from "../../../../library/api/state-middleware";
import config from "../../../../library/api/wp/config";
import GeneralTab from "./tabs/GeneralTab";
import SingleItemTab from "./tabs/SingleItemTab";
import SingleItemOverrideTab from "./tabs/SingleItemOverrideTab";

const ItemListMetaBoxList = ({session}) => {
    const [showModal, setShowModal] = useState(false);
    const [modalComponent, setModalComponent] = useState(null);
    const [modalHeader, setModalHeader] = useState(null);
    const [metaBoxContext, setMetaBoxContext] = useState({
        config: findMetaBoxConfig('item_list'),
        data: {
            singleItemPosts: []
        },
        formData: {
            item_list: [],
        },
        updateFormData: updateFormData,
        updateData: updateData,
        updateByKey: (key, value) => {
            setMetaBoxContext(state => {
                let cloneState = {...state};
                cloneState[key] = value;
                return cloneState;
            })
        }
    });
    const [isInitialized, setIsInitialized] = useState(false);

    function updateData(key, value) {
        setMetaBoxContext(state => {
            let cloneState = {...state};
            cloneState.data[key] = value;
            return cloneState;
        })
    }
    function updateFormData(key, value) {
        setMetaBoxContext(state => {
            let cloneState = {...state};
            cloneState.formData[key] = value;
            return cloneState;
        })
    }

    function updateItemListValue({value, key, index}) {
        const itemList = metaBoxContext.formData.item_list;
        const cloneItemList = [...itemList];
        cloneItemList[index][key] = value;
        updateFormData('item_list', cloneItemList);
    }

    function getTabConfig(index) {
        let tabConfig = [
            {
                name: 'general',
                title: 'General',
                component: GeneralTab
            },
            // {
            //     name: 'sidebar',
            //     title: 'Sidebar',
            //     component: SidebarTab
            // },
        ];
        switch (metaBoxContext.formData.item_list[index]?.type) {
            case 'single_item':
                tabConfig.push({
                    name: 'single_item',
                    title: 'Single Item',
                    component: SingleItemTab
                })
                break;
        }
        if (metaBoxContext.formData.item_list[index]?.override) {
            tabConfig.push({
                name: 'overrides',
                title: 'Overrides',
                component: SingleItemOverrideTab
            })
        }
        return tabConfig;
    }

    function getTabComponent(tab, item, index) {
        if (!tab?.component) {
            return null;
        }
        let TabComponent = tab.component;
        return <TabComponent
            index={index}
            formItem={item}
            onChange={({value, item}) => {
                updateItemListValue({
                    value,
                    key: item.name,
                    index,
                })
            }} />;
    }
    async function fetchSingleItemPostData() {
        const results = await fetchRequest({
            config: config,
            endpoint: config.endpoints.posts,
            params: {
                post_type: 'trf_single_item',
            }
        });

        const posts = results?.data?.posts;
        if (Array.isArray(posts)) {
            updateData('singleItemPosts', posts)
        }
    }


    useEffect(() => {
        if (!isInitialized) {
            return;
        }
        Object.keys(metaBoxContext.formData).forEach(field => {
            updateMetaHiddenFields({field, metaBoxContext, fieldGroupId: 'item_list'});
        })
    }, [metaBoxContext])

    useEffect(() => {
        updateInitialValues({fieldGroupId: 'item_list', metaBoxContext, setIsInitialized})
    }, [])

    useEffect(() => {
        fetchSingleItemPostData();
    }, []);

    function getFormGroup({item, index}) {
        return (
            <Tabs
                defaultActiveKey="1"
                items={ getTabConfig(index).map((tab, tabIndex) => {
                    return {
                        key: tabIndex,
                        label: tab.title,
                        children: getTabComponent(tab, item, index)
                    }
                })}
            />
        )
    }

    return (
        <Auth>
            <PostMetaBoxContext.Provider value={metaBoxContext}>
                <Row>
                    {metaBoxContext.formData.item_list.map((item, index) => {
                        return (
                            <Col key={index} span={24}>
                                {getFormGroup({item, index})}
                            </Col>
                        )
                    })}
                </Row>
                <Row>
                    <Col>
                        <Button
                            type={'primary'}
                            onClick={(e) => {
                                e.preventDefault();
                                updateFormData('item_list', [
                                    ...metaBoxContext.formData.item_list,
                                    {
                                        type: 'single_item',
                                        single_item_id: null,
                                        item_image: null,
                                        item_header: null,
                                        item_text: null,
                                        item_rating: null,
                                        item_link_text: null,
                                        item_link: null,
                                        item_badge_text: null,
                                        item_badge_link: null,
                                    }
                                ])
                            }}
                        >
                            Add Row
                        </Button>
                    </Col>
                </Row>
                <Modal
                    title={modalHeader}
                    open={showModal}
                    onOk={() => setShowModal(false)}
                    onCancel={() => setShowModal(false)}
                >

                    {modalComponent}
                </Modal>
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
)(ItemListMetaBoxList);
