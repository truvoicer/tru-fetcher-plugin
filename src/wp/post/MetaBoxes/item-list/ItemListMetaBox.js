import React from 'react';
import {Provider} from "react-redux";
import store from "../../../../library/redux/store";
import ItemListMetaBoxList from "./ItemListMetaBoxList";


const ItemListMetaBox = () => {
    return (
        <Provider store={store}>
            <ItemListMetaBoxList />
        </Provider>
    );
}

export default ItemListMetaBox;
