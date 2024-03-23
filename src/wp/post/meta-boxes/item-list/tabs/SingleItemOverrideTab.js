import React from 'react';
import CustomItemFormFields from "../../../components/item/CustomItemFormFields";

const GeneralTab = ({index, formItem, onChange}) => {
    return (
        <>
            <CustomItemFormFields formItem={formItem?.override || {}} onChange={({value, item, index}) => {
                const override = formItem?.override || {};
                let cloneOverride = {...override};
                cloneOverride[item.name] = value;
                onChange({value: cloneOverride, item: {name: 'override'}})
            }}/>
        </>
    );
};

export default GeneralTab;
