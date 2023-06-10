import React from 'react';
import {Provider} from "react-redux";
import store from "../../../library/redux/store";
import AppLoader from "../../../AppLoader";


const MetaBoxContainer = ({metaBoxComponent, apiConfig}) => {
    const MetaBoxComponent = metaBoxComponent;
    return (
        <Provider store={store}>
            <AppLoader apiConfig={apiConfig}>
                <MetaBoxComponent />
            </AppLoader>
        </Provider>
    );
}

export default MetaBoxContainer;
