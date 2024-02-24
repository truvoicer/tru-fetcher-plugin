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
        <div className={'tf--imagery--group'} style={{width: 200}}>
            <h1 style={{fontSize: 12, fontWeight: 700}}>{heading}</h1>
            <div className={'tf--imagery--group--media-controls'}>
                <MediaPicker
                    text={addImageText}
                    onSelect={(data) => {
                        onChange(data?.url)
                    }}
                />
                {selectedImageUrl
                    ? (
                        <>
                            <a
                                style={{width: 100, display: 'block'}}
                                onClick={(e) => {
                                    e.preventDefault()
                                }}>
                                <img
                                    style={{width: '100%', display: 'block'}}
                                    src={selectedImageUrl} alt={''}/>
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
