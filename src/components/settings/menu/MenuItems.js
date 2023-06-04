import React, {useEffect, useState} from 'react';
import {SESSION_STATE} from "../../../library/redux/constants/session-constants";
import {connect} from "react-redux";
import DataTable from "../../tables/DataTable";
import {STATE_CREATE, STATE_DELETE, STATE_INITIAL, STATE_UPDATE} from "../../../library/constants/constants";
import CategoryOptionsForm from "../../forms/CategoryOptionsForm";
import {getCheckboxInput} from "../../tables/fields";
import {Button} from "semantic-ui-react";
import {isNotEmpty, isObject, isObjectEmpty} from "../../../library/helpers/utils-helpers";
import {
    fetchAllOptionGroupsRequest,
    getOptionGroupSelectData
} from "../../../library/api/wp/requests/option-group-requests";
import optionsGroupItems from "../OptionsGroupItems";
import ScreenOptionsForm from "../../forms/menu/items/ScreenOptionsForm";
import ArticleOptionsForm from "../../forms/ArticleOptionsForm";
import AccessControlForm from "../../forms/menu/items/AccessControlForm";
import {updateMenuItemRolesRequest} from "../../../library/api/wp/requests/menu-requests";

const MenuItems = ({session, menu = null}) => {
    const [showCategoryOptionsFormModal, setShowCategoryOptionsFormModal] = useState(false);
    const [showArticleOptionsFormModal, setShowArticleOptionsFormModal] = useState(false);
    const [showScreenOptionsFormModal, setShowScreenOptionsFormModal] = useState(false);
    const [showAccessControlFormModal, setShowAccessControlFormModal] = useState(false);
    const [optionGroups, setOptionGroups] = useState([]);


    function menuItemsAsParentFormDataCallback({endpointsObject, setItems, setFormData}) {
        const menuItemsResults = endpointsObject?.menuItems?.data;
        if (!Array.isArray(menuItemsResults)) {
            return;
        }
        const menuItems = menuItemsResults.map(menuItem => {

            let categoryOptions = {};
            if (isNotEmpty(menuItem?.categoryOptions) && isObject(menuItem.categoryOptions) && !isObjectEmpty(menuItem.categoryOptions)) {
                categoryOptions.categoryOptions = menuItem.categoryOptions;
            }
            return {
                state: STATE_UPDATE,
                ...menuItem,
                ...categoryOptions
            };
        });

        setFormData({
            items: menuItems
        })
    }

    return (
        <div>
            <DataTable
                heading={'Menu Items'}
                itemStructure={{
                    name: null,
                    initial_screen: null,
                    active: null,
                    show_when_logged_in: null,
                    type: null,
                    screen: null,
                    post_id: null,
                    articles_show_all: null,
                    articles_sort_by: null,
                    articles_sort_order: null,
                    featured_articles_show: null,
                    featured_articles_show_multiple: null,
                    featured_articles_multiple_mode: null,
                    featured_articles_slideshow_timer: null,
                    category_options_override: false,
                    categoryOptions: null,
                    accessControl: null
                }}
                columns={[
                    {
                        name: 'Name',
                        dataKey: 'name',
                    },
                    {
                        name: 'Access Control Options',
                        render: ({formData, fieldData, column, formikProps, formChangeCallback}) => {
                            return (
                                <Button
                                    type={'button'}
                                    onClick={e => {
                                        AccessControlForm.defaultProps = {
                                            formikProps,
                                            formData,
                                            fieldData,
                                            updateCallback: async ({values, fieldData, formData, setMessage}) => {
                                                let cloneValues = {...values};
                                                const menuItem = formData.items[fieldData.index];
                                                switch (menuItem['state']) {
                                                    case 'update':
                                                        const menuId = menuItem['menu_id'];
                                                        const menuItemId = menuItem['id'];
                                                        const results = await updateMenuItemRolesRequest({
                                                            menuId: menuId,
                                                            menuItemId: menuItemId,
                                                            data: values
                                                        })
                                                        const menuItemRoles = results?.data?.roles;

                                                        if (Array.isArray(menuItemRoles)) {
                                                            setMessage('Access Control roles Updated');
                                                            cloneValues.roles = menuItemRoles;
                                                        }
                                                        break;
                                                }
                                                return cloneValues;
                                            }
                                        }
                                        setShowAccessControlFormModal(true)
                                    }}
                                >
                                    Access Control Options
                                </Button>
                            );
                        }
                    },
                    {
                        name: 'Screen Options',
                        render: ({formData, fieldData, column, formikProps, formChangeCallback}) => {
                            return (
                                <Button
                                    type={'button'}
                                    onClick={e => {
                                        ScreenOptionsForm.defaultProps = {
                                            formikProps,
                                            formData,
                                            fieldData
                                        }
                                        setShowScreenOptionsFormModal(true)
                                    }}
                                >
                                    Screen Options
                                </Button>
                            );
                        }
                    },
                    {
                        name: 'Article Options',
                        render: ({formData, fieldData, column, formikProps, formChangeCallback}) => {
                            return (
                                <Button
                                    type={'button'}
                                    onClick={e => {
                                        ArticleOptionsForm.defaultProps = {
                                            formikProps,
                                            formData,
                                            fieldData
                                        }
                                        setShowArticleOptionsFormModal(true)
                                    }}
                                >
                                    Article Options
                                </Button>
                            );
                        }
                    },
                    {
                        name: 'Category Options Override',
                        dataKey: 'category_options_override',
                        fieldElement: 'checkbox',
                        render: ({formData, fieldData, column, formikProps, formChangeCallback}) => {
                            const checkbox = getCheckboxInput({
                                fieldData,
                                column,
                                formikProps,
                                formChangeCallback: formChangeCallback,
                            });

                            return (
                                <>
                                    {checkbox}
                                    {fieldData?.value &&
                                        <Button
                                            type={'button'}
                                            onClick={e => {
                                                CategoryOptionsForm.defaultProps = {
                                                    formikProps,
                                                    formData,
                                                    fieldData
                                                }
                                                setShowCategoryOptionsFormModal(true)
                                            }}
                                        >
                                            Category Options
                                        </Button>
                                    }
                                </>
                            );
                        }
                    },
                ]}
                fetchEndpoint={[{
                    name: 'menuItems',
                    endpoint: `menu/${menu.id}/menu/items`,
                    objectListKey: 'menuItems'
                }]}
                formDataCallback={menuItemsAsParentFormDataCallback}
                updateEndpoint={({data}) => {
                    if (!isNaN(data?.id)) {
                        console.log(data?.id)
                        return `menu/${menu.id}/items/${data.id}/update`;
                    }
                    return false;
                }}
                createEndpoint={`menu/${menu.id}/items/create`}
                deleteEndpoint={`menu/${menu.id}/items/delete`}
                saveBatchEndpoint={(menu?.id) ? `menu/${menu.id}/items/save` : `menu/items/save`}
                deleteItemCompareKeys={['id']}
                objectListKey={'menuItems'}
                objectItemKey={'menuItems'}
                idKey={'id'}
            ></DataTable>

            <CategoryOptionsForm
                modal={true}
                showFormModal={showCategoryOptionsFormModal}
                setShowFormModal={setShowCategoryOptionsFormModal}
            />
            <ArticleOptionsForm
                modal={true}
                showFormModal={showArticleOptionsFormModal}
                setShowFormModal={setShowArticleOptionsFormModal}
            />
            <ScreenOptionsForm
                modal={true}
                showFormModal={showScreenOptionsFormModal}
                setShowFormModal={setShowScreenOptionsFormModal}
            />
            <AccessControlForm
                modal={true}
                showFormModal={showAccessControlFormModal}
                setShowFormModal={setShowAccessControlFormModal}
            />
        </div>
    );
};

export default connect(
    (state) => {
        return {
            session: state[SESSION_STATE]
        }
    },
    null
)(MenuItems);
