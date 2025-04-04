export class GutenbergBase {
    static filterBlocksByBlockName(blockName, blocks) {
        return blocks.filter(block => block?.name === blockName);
    }
    static findBlockById(blockId) {
        const findBlock = tru_fetcher_react.blocks.find(block => block?.id === blockId);
        if (!findBlock) {
            return false;
        }
        return findBlock;
    }
    static getSelectOptions(id, props) {
        let options = [
            {
                label: 'Please Select',
                value: ''
            }
        ]
        if (!Array.isArray(props?.child?.attributes)) {
            return options;
        }
        const findAttribute = props.child.attributes.find(attribute => attribute.id === id);
        if (!Array.isArray(findAttribute?.options)) {
            return options;
        }
        return [
            ...options,
            ...findAttribute.options
        ];
    }
}
