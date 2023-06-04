
import React from 'react';
import { TextControl, TextareaControl, ToggleControl, DateTimePicker, ColorPicker, Button } from '@wordpress/components';
export const FIELDS = {
    TEXT: 'text',
    HTML: 'html',
    URL: 'url',
    DATE: 'date',
    NUMBER: 'number',
    TRUE_FALSE: 'true_false',
    COLOR: 'color',
    IMAGE: 'image',
    IMAGE_LIST: 'image_list',
    ARRAY: 'array',
};
export function getTextField({changeHandler, type, label, value}) {
    return (
        <TextControl
            label={ label }
            value={ value }
            type={ type }
            onChange={ ( textValue ) => changeHandler( textValue ) }
        />
    )
}

export function getArrayField({changeHandler, label, value}) {
}

export function getHtmlField({changeHandler, label, value}) {
    return (
        <TextareaControl
            label={ label }
            help="Enter some text"
            value={ text }
            onChange={ ( value ) => changeHandler( value ) }
        />
    )
}

export function getDateField({changeHandler, label, setModalComponent, setShowModal, value}) {

    return (
        <Button
            variant="primary"
            onClick={() => {
                setModalComponent(<DateTimePicker  onChange={(date) => { changeHandler( date ) }} />);
                setShowModal(true);
            }}
        >
            Select Date
        </Button>
    )
}

export function getColorField({changeHandler, label, setModalComponent, setShowModal, value}) {

    return (

        <Button
            variant="primary"
            onClick={() => {
                setModalComponent(<ColorPicker onChange={(color) => { changeHandler( color ) }} />);
                setShowModal(true);
            }}
        >
            Select Color
        </Button>
    )
}

export function getTrueFalseField({changeHandler, label, value}) {
    return (
        <ToggleControl
            label={ label }
            checked={ value }
            onChange={ () => changeHandler( ! value ) }
        />
    );
}

export function getImageField({changeHandler, label}) {


}

export function getImageListField({changeHandler, label}) {

}


export default function buildFormField ({fieldType, value, changeHandler, setModalComponent, setShowModal}) {
    let fieldProps = {};
    fieldProps.value = value;
    fieldProps.changeHandler = changeHandler;
    fieldProps.setModalComponent = setModalComponent;
    fieldProps.setShowModal = setShowModal;
    switch (fieldType) {
        case FIELDS.TEXT:
            fieldProps.label = 'Text';
            fieldProps.type = 'text';
            return getTextField(fieldProps);
        case FIELDS.URL:
            fieldProps.label = 'Url';
            fieldProps.type = 'url';
            return getTextField(fieldProps);
        case FIELDS.NUMBER:
            fieldProps.label = 'Number';
            fieldProps.type = 'number';
            return getTextField(fieldProps);
        case FIELDS.DATE:
            fieldProps.label = 'Date';
            return getDateField(fieldProps);
        case FIELDS.HTML:
            fieldProps.label = 'HTML';
            return getHtmlField(fieldProps);
        case FIELDS.COLOR:
            fieldProps.label = 'Color Picker';
            return getColorField(fieldProps);
        case FIELDS.TRUE_FALSE:
            fieldProps.label = 'True/False';
            return getTrueFalseField(fieldProps);
        case FIELDS.IMAGE:
            fieldProps.label = 'Image';
            return getImageField(fieldProps)
        case FIELDS.IMAGE_LIST:
            fieldProps.label = 'Image List';
            return getImageListField(fieldProps);
        case FIELDS.ARRAY:
            fieldProps.label = 'Array';
            return getArrayField(fieldProps);
        default:
            return null;
    }
}
