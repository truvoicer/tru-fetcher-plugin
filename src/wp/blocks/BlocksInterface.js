import React from 'react';
import ListingsBlockEdit from "./listings/ListingsBlockEdit";

const BlocksInterface = (props) => {
    function getBlock() {
        switch (props?.config?.id) {
            case "listings-block":
                return <ListingsBlockEdit {...props} />
            default:
                return null;
        }

    }
    return getBlock();
};

export default BlocksInterface;
