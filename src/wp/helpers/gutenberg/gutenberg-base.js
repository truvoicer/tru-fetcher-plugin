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
}
