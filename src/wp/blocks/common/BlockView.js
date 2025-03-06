import React from 'react';
import {Panel, PanelBody} from "@wordpress/components";


const BlockView = (props) => {
    const {
        attributes,
        setAttributes,
        className,
        viewConfig = []
    } = props;

    return (
        <div>
            {viewConfig.map((item, index) => {
                return (
                    <Panel key={index}>
                        <PanelBody title={item?.title || ''} initialOpen={item?.open}>
                            {Array.isArray(item?.children) && item.children.length &&
                                <ul style={{marginLeft: 0, paddingLeft: 0}}>
                                    {item.children.map((childItem, childItemIndex) => {
                                        if (!childItem?.name || !childItem.name.length) {
                                            return null;
                                        }
                                        if (!childItem?.key) {
                                            return null;
                                        }
                                        if (typeof childItem.key !== 'function' && typeof childItem.key !== 'string') {
                                            return null;
                                        }
                                        if ((typeof childItem.key === 'string') && !childItem.key.length && !attributes.hasOwnProperty(childItem.key)) {
                                            return null;
                                        }
                                        return (
                                            <li key={childItemIndex} style={{display: 'block', textAlign: 'center', marginTop: 10}}>
                                                <div style={{fontWeight: 'bold'}}>
                                                    {childItem.name} :
                                                </div>
                                                <div style={{marginLeft: 5}}>
                                                    {typeof childItem.key === 'function'
                                                    ? childItem.key()
                                                    : attributes[childItem.key]
                                                }
                                                </div>
                                            </li>
                                        );
                                    })}
                                </ul>
                            }
                        </PanelBody>
                    </Panel>
                );
            })}
        </div>
    );
};

export default BlockView;
