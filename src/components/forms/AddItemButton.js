import React from 'react';
import {Icon} from "semantic-ui-react";

const AddItemButton = ({onClick, positionClass = 'tr-news-app__form--add-btn--left-bottom'}) => {
    return (
        <a
            className={`tr-news-app__form--add-btn ${positionClass}`}
            onClick={onClick}
        >
            <Icon name={'add circle'}/>
        </a>
    );
};

export default AddItemButton;
