import React from 'react';
import {__experimentalGrid as EGrid} from "@wordpress/components";

const Grid = ({children, ...otherProps}) => {
    return (
        <EGrid {...otherProps}>
            {children}
        </EGrid>
    );
};

export default Grid;
