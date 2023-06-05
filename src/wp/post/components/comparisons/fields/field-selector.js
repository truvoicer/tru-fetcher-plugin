
import React from 'react';
import { Input, DatePicker, Button, Switch, ColorPicker  } from 'antd';
const { TextArea } = Input;
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
        <Input
            placeholder={ label }
            value={ value }
            type={ type }
            onChange={ ( e ) => changeHandler( e.target.value ) }
        />
    )
}

export function getArrayField({changeHandler, label, value}) {
    return null;
}

export function getHtmlField({changeHandler, label, value}) {
    return (
        <TextArea
            placeholder={ label }
            value={ value }
            onChange={ ( e ) => changeHandler( e.target.value ) }
        />
    )
}

export function getDateField({changeHandler, label, setModalComponent, setShowModal, value}) {

    return (
        <DatePicker  onChange={(date, dateString) => { changeHandler( dateString ) }} />
    )
}

export function getColorField({changeHandler, label, setModalComponent, setShowModal, value}) {

    return (

        <Button
            type="primary"
            onClick={() => {
                setModalComponent(<ColorPicker  onChange={(value, hex) => { changeHandler( hex ) }} />);
                setShowModal(true);
            }}
        >
            Select Color
        </Button>
    )
}

export function getTrueFalseField({changeHandler, label, value}) {
    return (
        <Switch
            checkedChildren="Yes" unCheckedChildren="No"
            checked={ value }
            onChange={ () => changeHandler( ! value ) }
        />
    );
}

export function getImageField({changeHandler, label}) {
    return null;
}

export function getImageListField({changeHandler, label}) {

    return null;
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
