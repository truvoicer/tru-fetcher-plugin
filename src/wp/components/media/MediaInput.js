import React from 'react';
import MediaPicker from "./MediaPicker";

const MediaInput = ({
    heading = '',
    addImageText = 'Add Image',
    noImageText = 'No Image Selected',
    deleteText = 'Delete',
    selectedImageUrl = null,
    onChange,
    onDelete
}) => {
    return (
        <div className={'tf--imagery--group'}>
            <h3>{heading}</h3>
            <div className={'tf--imagery--group--media-controls'}>
                <MediaPicker
                    text={addImageText}
                    onSelect={(data) => {
                        console.log(data?.url)
                        onChange(data?.url)
                    }}
                />
                {selectedImageUrl
                    ? (
                        <>
                            <a
                                onClick={(e) => {
                                    e.preventDefault()
                                }}>
                                <img src={selectedImageUrl} alt={''}/>
                            </a>
                            <a
                                onClick={(e) => {
                                    e.preventDefault()
                                    onDelete()
                                }}>
                                {deleteText}
                            </a>
                        </>
                    )
                    : (
                        <p>{noImageText}</p>
                    )
                }
            </div>
        </div>
    );
};

export default MediaInput;
