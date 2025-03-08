import React from 'react';
import {TextareaControl} from "@wordpress/components";
import {InspectorAdvancedControls, useBlockProps} from '@wordpress/block-editor';

import { InspectorControls } from '@wordpress/block-editor';
import BlockView from '../common/BlockView';
import { isObject, isObjectEmpty, toSnakeCase } from '../../../library/helpers/utils-helpers';


const BlockEditComponent = (props) => {
    const {
        attributes,
        setAttributes,
        title,
        advancedControls = null,
        containerProps = null,
        viewConfig = {},
        children,

    } = props;

    function getContainerProps() {
        if (typeof containerProps !== 'function') {
            return useBlockProps();
        }
        return containerProps();
    }
    function renderAdvancedControls() {
        if (typeof advancedControls === 'function') {
            return advancedControls();
        }
        return null;
    }
    return (
        <div {...getContainerProps()}>
            <InspectorControls key={toSnakeCase(title)}>
                {children}
            </InspectorControls>
            <InspectorAdvancedControls>
                <TextareaControl
					__nextHasNoMarginBottom
					__next40pxDefaultSize
                    help="Enter additional styles"
                    label="Additional Styles"
                    value={attributes?.additional_styles}
                    onChange={(value) => setAttributes({ additional_styles: value })}
                />
                {renderAdvancedControls()}
            </InspectorAdvancedControls>
            {isObject(viewConfig) && !isObjectEmpty(viewConfig)
                ? <BlockView
                    {...props}
                    viewConfig={viewConfig} />
                : <h1>{title}</h1>
            }
        </div>
    );
};

export default BlockEditComponent;
