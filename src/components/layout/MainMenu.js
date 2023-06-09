import React, {useState} from 'react'
import {SESSION_STATE} from "../../library/redux/constants/session-constants";
import {APP_ACTIVE_MENU_ITEM, APP_STATE} from "../../library/redux/constants/app-constants";
import {connect} from 'react-redux';
import {Menu, Dropdown, Button} from "antd";
import routeConfig from "../../library/routes/route-config";
import {useMatches, useNavigate} from "react-router-dom";
import {setActiveMenuItemAction} from "../../library/redux/actions/app-actions";
import {buildRoutePathByKeyValue} from "../../library/helpers/route-helpers";

function MainMenu({app, session}) {
    const navigate = useNavigate();
    function menuClickHandler(name) {
        setActiveMenuItemAction(name)
        const getNextRoute = buildRoutePathByKeyValue('key', name, true);
        navigate(getNextRoute)
    }
    function handleDropdownItemClick(e, {value}) {
        menuClickHandler(value)
    }
    function handleMenuItemClick(e, {name}) {
        menuClickHandler(name)
    }
    function buildRouteItem(route, index, type) {
        return (
            <Menu.Item
                key={`${type}_${index}`}
                name={route?.key}
                active={app[APP_ACTIVE_MENU_ITEM] === route?.key}
                onClick={handleMenuItemClick}>
                {route?.label}
            </Menu.Item>
        )
    }
    return (
        <div>
            <Menu pointing secondary>
            {routeConfig.map((route, index) => {
                if (Array.isArray(route?.subRoutes)) {
                    return (
                        <Dropdown item text={route?.label} key={index}>
                            <Dropdown.Menu>
                                {route?.subRoutes.map((subRoute, index) => {
                                    return (
                                        <Dropdown.Item value={subRoute?.key} key={index} onClick={handleDropdownItemClick}>
                                            {subRoute?.label}
                                        </Dropdown.Item>
                                    );
                                })}
                            </Dropdown.Menu>
                        </Dropdown>
                    )
                }
                return buildRouteItem(route, index, 'menu')
            })}
            </Menu>
        </div>
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
)(MainMenu);
