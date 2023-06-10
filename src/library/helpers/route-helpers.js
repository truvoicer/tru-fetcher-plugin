import React from 'react';
import Auth from "../../components/auth/Auth";
import Template from "../../components/layout/Template";
import store from "../redux/store";
import {APP_NAME, APP_STATE} from "../redux/constants/app-constants";
import routeConfig from "../routes/route-config";
import AdminTemplate from "../../components/layout/AdminTemplate";

const ROUTE_SUBS = 'subRoutes';

export function buildRouterData(routes) {
    let buildRoutes = [];
    routes.forEach((route, index) => {
        let path;
        path = route?.path;
        if (Array.isArray(route?.subRoutes)) {
            buildRoutes = [...buildRoutes, ...buildRouterData(route.subRoutes)];
        }
        buildRoutes.push({
            id: route?.key,
            path,
            element: buildSubComponent(route.component),
        })
    });
    return buildRoutes;
}

export function buildSubComponent(component, props = {}) {
    const Component = component;
    return (
        <AdminTemplate>
            <Component {...props} />
        </AdminTemplate>
    )
}

export function buildRoutePathByKeyValue(key, value, includeSuffix = false) {
    const getRoute = buildRouteTree(
        routeConfig,
        key,
        value,
    );
    return buildRoutePath(getRoute, includeSuffix);
}

export function buildRoutePathByRouteObject(route, includeSuffix = false) {
    if (route?.path && route.path !== '') {
        return buildRoutePath(route.path)
    }
    return false;
}

export function buildRoutePath(routes, includeSuffix = false) {
    const routeNames = routes.map(route => {
        if (route?.home) {
            return '/';
        }
        return route?.key
    });
    const routeUrl = routeNames.join('/');
    const routeSuffix = (!includeSuffix) ? '' : buildRouteSuffix();
    if (!routeUrl || routeUrl === '' || routeUrl === '/') {
        return `/${!(routeSuffix) ? '' : routeSuffix}`;
    }
    return `/${routeUrl}`;
}

function buildRouteSuffix() {
    const appState = store.getState()[APP_STATE];
    if (!appState[APP_NAME]) {
        return false;
    }
    return `?page=${appState[APP_NAME]}`
}

export function buildRouteTree(items, key, value, prevRoutes = []) {
    for (let i = 0; i < items.length; i++) {
        if (typeof items[i][key] === 'undefined') {
            continue;
        }
        if (items[i][key] === value) {
            prevRoutes.push(items[i]);
            return prevRoutes;
        }
        if (typeof items[i][ROUTE_SUBS] === "undefined") {
            continue;
        }
        const route = {...items[i]};
        delete route[ROUTE_SUBS];
        const buildTree = buildRouteTree(items[i][ROUTE_SUBS], key, value, prevRoutes);
        if (typeof buildTree === "undefined") {
            continue;
        }
        prevRoutes.push(route)
        return prevRoutes.reverse();
    }
}
