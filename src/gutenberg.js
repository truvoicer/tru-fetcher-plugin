import { registerPlugin, getPlugin } from '@wordpress/plugins';
import { registerBlockType } from '@wordpress/blocks';
import { useeffect } from '@wordpress/element';
import 'antd/dist/reset.css';
import '../assets/sass/tru-fetcher-admin.scss';
import SidebarMetaBoxLoader from "./wp/sidebar/SidebarMetaBoxLoader";

import BlocksInterface from "./wp/blocks/BlocksInterface";

if (!getPlugin('trf-fetcher-plugin')) {
    registerPlugin( 'trf-metadata-plugin', {
        render: SidebarMetaBoxLoader
    } );
}
/**
 * WordPress dependencies
 */


// Export this so we can use it in the edit and save files
export const blockStyle = {
    backgroundColor: '#900',
    color: '#fff',
    padding: '20px',
};
console.log(tru_fetcher_react)
if (
    typeof tru_fetcher_react !== 'undefined' &&
    typeof tru_fetcher_react.blocks !== 'undefined' &&
    Array.isArray(tru_fetcher_react.blocks)
) {
    tru_fetcher_react.blocks.forEach((block) => {
        let attData = {};
        let examplesAttData = {};
        if (typeof block.attributes !== 'undefined' && Array.isArray(block.attributes)) {
            block.attributes.forEach((attribute) => {
                attData[attribute.id] = {
                    type: attribute.type,
                    default: attribute.default,
                };
                examplesAttData[attribute.id] = attribute.default;
            });
        }
        BlocksInterface.defaultProps = {
            config: block,
            apiConfig: tru_fetcher_react.api,
        }
        registerBlockType( block.name, {
            title: block.title,
            attributes: attData,
            example: {
                attributes: examplesAttData
            },
            edit: BlocksInterface,
        } );
    });
}
