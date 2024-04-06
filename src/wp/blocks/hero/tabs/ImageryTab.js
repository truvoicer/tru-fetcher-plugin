import React from 'react';
import ImageListComponent from "../../components/image-list/ImageListComponent";

const ImageryTab = (props) => {
    const {
        attributes,
        setAttributes,
        className,
        apiConfig,
        wpMediaFrame,
    } = props;

    return (
        <div className={'tf--imagery wrap'} style={{display: 'flex', flexDirection: 'row', flexWrap: 'wrap'}}>
            <ImageListComponent data={attributes?.images || []} onChange={(value) => {
                setAttributes({images: value});
            }}/>
        </div>
    );
};

export default ImageryTab;
