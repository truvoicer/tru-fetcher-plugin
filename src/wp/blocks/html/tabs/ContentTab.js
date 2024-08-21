import React from 'react';

import { useBlockProps, RichText } from '@wordpress/block-editor';
import {TabPanel, Panel, PanelBody, TextControl, SelectControl, ToggleControl} from "@wordpress/components";
import {isNotEmpty, isObject} from "../../../../library/helpers/utils-helpers";
import TreeSelectList from "../../../../components/forms/TreeSelectList";

const ContentTab = (props) => {
    const {
        attributes,
        setAttributes,
        className,
        apiConfig
    } = props;

    const blockProps = useBlockProps();

    // function getFileTypeTreeIdByName(name) {
    //     if (!isNotEmpty(name)) {
    //         return null;
    //     }
    //     if (!isObject(name)) {
    //         return null;
    //     }
    //
    //     if (!Array.isArray(tru_fetcher_react?.media?.file_types)) {
    //         return null;
    //     }
    //
    //     for (let i = 0; i < tru_fetcher_react?.media?.file_types.length; i++) {
    //         let type = tru_fetcher_react?.media?.file_types[i];
    //         if (name?.parent && type?.name === name?.name) {
    //             return buildTypeId(type?.name, i);
    //         }
    //         if (Array.isArray(type?.types)) {
    //             for (let j = 0; j < type?.types.length; j++) {
    //                 let extension = type?.types[j];
    //                 if (extension?.name === name?.name) {
    //                     return buildExtensionId(type?.name, extension?.name, j);
    //                 }
    //             }
    //         }
    //     }
    //     return null;
    // }
    // function buildTypeId(typeName, index) {
    //     return `${typeName}_${index}`;
    // }
    // function buildExtensionId(typeName, extensionName, index) {
    //     return `${typeName}_${extensionName}_${index}`;
    // }
    // function buildFileTypeTree() {
    //     if (!Array.isArray(tru_fetcher_react?.media?.file_types)) {
    //         return [];
    //     }
    //     const fileTypes = tru_fetcher_react?.media?.file_types;
    //     return [
    //         ...fileTypes.map((type, index) => {
    //             let treeItem = {
    //                 name: type?.name,
    //                 id: buildTypeId(type?.name, index),
    //             };
    //             if (Array.isArray(type?.types)) {
    //                 treeItem.children = type.types.map((extension, extIndex) => {
    //                     return {
    //                         name: extension?.name,
    //                         id: buildExtensionId(type?.name, extension?.name, extIndex),
    //                     }
    //                 });
    //             }
    //             return treeItem;
    //         })
    //     ];
    // }
    return (
        <div>
            {/*<TreeSelectList*/}
            {/*    selectedId={getFileTypeTreeIdByName(fileType?.type)}*/}
            {/*    treeData={buildFileTypeTree()}*/}
            {/*    label={'Select File Type'}*/}
            {/*    onChange={(selectedName) => {*/}
            {/*        updateFormItem({*/}
            {/*            rowIndex,*/}
            {/*            formItemIndex,*/}
            {/*            field: 'allowed_file_types',*/}
            {/*            value: selectedName,*/}
            {/*            isArray: true,*/}
            {/*            arrayIndex: index,*/}
            {/*            arrayKey: 'type'*/}
            {/*        });*/}
            {/*    }}*/}
            {/*/>*/}
            <SelectControl
                label="Hero Type"
                onChange={(value) => {
                    setAttributes({hero_type: value});
                }}
                value={attributes?.hero_type}
                options={[
                    {
                        disabled: true,
                        label: 'Select an Option',
                        value: ''
                    },
                    {
                        label: 'Full Hero',
                        value: 'full_hero'
                    },
                    {
                        label: 'Breadcrumb Hero',
                        value: 'breadcrumb_hero'
                    },
                ]}
            />
            <RichText
                { ...blockProps }
                tagName="h2" // The tag here is the element output and editable in the admin
                value={ attributes.content } // Any existing content, either from the database or an attribute default
                allowedFormats={ [ 'core/bold', 'core/italic', 'tru-fetcher-format/placeholder-button' ] } // Allow the content to be made bold or italic, but do not allow other formatting options
                onChange={ ( content ) => setAttributes( { content } ) } // Store updated content as a block attribute
                placeholder={'Enter content here...'} // Display this text before any content has been added by the user
            />
        </div>
    );
};

export default ContentTab;
