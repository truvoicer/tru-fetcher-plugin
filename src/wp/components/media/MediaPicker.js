import React from 'react';
import {Button} from "@wordpress/components";

const MediaPicker = ({onSelect, text}) => {

    let wpMediaFrame;
    wpMediaFrame = wp.media({
        title: 'Select or Upload Media Of Your Chosen Persuasion',
        button: {
            text: 'Use this media'
        },
        multiple: false  // Set to true to allow multiple files to be selected
    });
    // When an image is selected in the media frame...
    wpMediaFrame.on('select', function () {
        // Get media attachment details from the frame state
        var data = wpMediaFrame.state().get('selection').first().toJSON();
        if (typeof onSelect === 'function') {
            onSelect(data);
        }
    });
    return (
        <Button
            className={'button'}
            onClick={(e) => {
                if (wpMediaFrame) {
                    wpMediaFrame.open();
                }
            }}
        >
            {text}
        </Button>
    );
};

export default MediaPicker;
