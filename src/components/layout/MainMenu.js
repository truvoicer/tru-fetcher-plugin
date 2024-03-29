import React, {useState, useEffect} from 'react'
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
    const [menuItems, setMenuItems] = useState([]);

    function buildMenuItems() {
        const items = [];
        routeConfig.forEach((route, index) => {
            if (Array.isArray(route?.subRoutes)) {
                const subItems = [];
                route?.subRoutes.forEach((subRoute, index) => {
                    subItems.push({
                        key: subRoute?.key,
                        label: subRoute?.label,
                        value: subRoute?.key,
                    })
                })
                items.push({
                    key: route?.key,
                    label: route?.label,
                    children: subItems,
                })
            } else {
                items.push({
                    key: route?.key,
                    label: route?.label,
                    value: route?.key,
                })
            }
        })
        setMenuItems(items)

    }

    useEffect(() => {
        buildMenuItems()
    }, []);
    function menuClickHandler(name) {
        setActiveMenuItemAction(name)
        const getNextRoute = buildRoutePathByKeyValue('key', name, true);
        navigate(getNextRoute)
    }

    function handleMenuItemClick(e) {
        menuClickHandler(e.key)
    }

    return (
        <Menu
            onClick={handleMenuItemClick}
            selectedKeys={[app[APP_ACTIVE_MENU_ITEM]]}
            mode="horizontal"
            items={menuItems}
        />
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
