import React from 'react';
import {Provider} from "react-redux";
import store from "../../../library/redux/store";


const MetaBoxContainer = ({metaBoxComponent}) => {
    const MetaBoxComponent = metaBoxComponent;
    return (
        <Provider store={store}>
            <MetaBoxComponent />
        </Provider>
    );
}

export default MetaBoxContainer;
