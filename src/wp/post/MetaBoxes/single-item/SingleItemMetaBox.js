import React from 'react';
import {Provider} from "react-redux";
import store from "../../../../library/redux/store";
import SingleItemMetaBoxTabs from "./SingleItemMetaBoxTabs";


const SingleItemMetaBox = () => {
    return (
        <Provider store={store}>
            <SingleItemMetaBoxTabs />
        </Provider>
    );
}

export default SingleItemMetaBox;
