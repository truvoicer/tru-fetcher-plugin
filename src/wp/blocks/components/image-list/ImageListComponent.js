import React from 'react';
import {Button, Panel, PanelBody} from "@wordpress/components";
import {Icon, chevronDown, chevronUp, trash} from "@wordpress/icons";
import MediaInput from "../../../components/media/MediaInput";

const ImageListComponent = ({data = [], onChange}) => {
    function addParam() {
        let cloneSearchParam = [...data];
        cloneSearchParam.push({src: ''});
        onChange(cloneSearchParam);
    }

    function updateParam({index, key, value}) {
        let cloneSearchParam = [...data];
        cloneSearchParam[index][key] = value;
        onChange(cloneSearchParam);
    }

    function deleteParam({index}) {
        let cloneSearchParam = [...data];
        cloneSearchParam.splice(index, 1);
        onChange(cloneSearchParam);
    }

    return (
        <>
            {data.map((param, index) => {
                return (
                    <div className="tf--list--item tf--list--item--no-header" style={{display: 'flex'}}>
                        <div className="tf--list--item--content" style={{flex: 1}}>
                            <Panel>
                                <PanelBody title={`Image (${index})`} initialOpen={true}>
                                    <MediaInput
                                        hideDelete={true}
                                        heading={`Image (${index})`}
                                        addImageText={'Add'}
                                        selectedImageUrl={data[index].src}
                                        onChange={(value) => {
                                            updateParam({
                                                index,
                                                key: 'src',
                                                value
                                            })
                                        }}
                                        onDelete={(value) => {
                                            deleteParam({index});
                                        }}
                                    />
                                </PanelBody>
                            </Panel>
                        </div>
                        <div className={'tf--list--item--actions'} style={{
                            display: 'flex',
                            flexDirection: 'column',
                            borderRight: '1px solid #e0e0e0',
                            borderTop: '1px solid #e0e0e0',
                            borderBottom: '1px solid #e0e0e0',
                        }}>
                            <a style={{cursor: 'pointer'}} onClick={(e) => {
                                e.preventDefault()
                                deleteParam({index});
                            }}>
                                <Icon icon={trash}/>
                            </a>
                        </div>
                    </div>
                );
            })}
            <Button
                variant="primary"
                onClick={(e) => {
                    e.preventDefault()
                    addParam()
                }}
            >
                Add New
            </Button>
        </>
    );
};

export default ImageListComponent;
