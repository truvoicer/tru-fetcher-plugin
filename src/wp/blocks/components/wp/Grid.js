import React from 'react';

const Grid = ({children, columns = 1}) => {
    // grid-template-columns: repeat(4, 1fr);
    function buildStyleObject() {
        return {
            gridTemplateColumns: `repeat(${columns}, 1fr)`
        }
    }
    return (
        <div className={'tf--grid'} style={buildStyleObject()}>
            {children}
        </div>
    );
};

export default Grid;
