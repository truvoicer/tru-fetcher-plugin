import { registerPlugin } from '@wordpress/plugins';
import { registerBlockType } from '@wordpress/blocks';
import 'antd/dist/reset.css';
import '../assets/sass/tru-fetcher-admin.scss';
import SidebarMetaBoxLoader from "./wp/sidebar/SidebarMetaBoxLoader";

import ListingsBlockEdit from "./wp/blocks/listings/ListingsBlockEdit";
import ListingsBlockSave from "./wp/blocks/listings/ListingsBlockSave";

registerPlugin( 'metadata-plugin', {
    render: SidebarMetaBoxLoader
} );
/**
 * WordPress dependencies
 */


// Export this so we can use it in the edit and save files
export const blockStyle = {
    backgroundColor: '#900',
    color: '#fff',
    padding: '20px',
};

// Register the block
registerBlockType( 'tru-fetcher/listings-block', {
    edit: ListingsBlockEdit,
    save: ListingsBlockSave, // Object shorthand property - same as writing: save: save,
} );
