import React from 'react';
import MediaInput from "../../../components/media/MediaInput";

const ImageryTab = (props) => {
    const {
        attributes,
        setAttributes,
        className,
        apiConfig,
        wpMediaFrame,
    } = props;

    return (
        <div className={'tf--imagery'}>
            <MediaInput
              heading={'Hero Background Image'}
                addImageText={'Add Image'}
                selectedImageUrl={attributes.hero_background_image}
                onChange={(value) => {
                    setAttributes({
                        hero_background_image: value
                    })
                }}
                onDelete={(value) => {
                    setAttributes({
                        hero_background_image: ''
                    })
                }}
            />
            <MediaInput
              heading={'Hero Background Image 2'}
                addImageText={'Add Image'}
                selectedImageUrl={attributes?.hero_background_image_2}
                onChange={(value) => {
                    setAttributes({
                        hero_background_image_2: value
                    })
                }}
                onDelete={(value) => {
                    setAttributes({
                        hero_background_image_2: ''
                    })
                }}
            />
            <MediaInput
              heading={'Hero Background Image 3'}
                addImageText={'Add Image'}
                selectedImageUrl={attributes?.hero_background_image_3}
                onChange={(value) => {
                    setAttributes({
                        hero_background_image_3: value
                    })
                }}
                onDelete={(value) => {
                    setAttributes({
                        hero_background_image_3: ''
                    })
                }}
            />
        </div>
    );
};

export default ImageryTab;
