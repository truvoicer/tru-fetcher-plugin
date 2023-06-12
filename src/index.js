
import { registerPlugin, getPlugin } from '@wordpress/plugins';
import { registerBlockType } from '@wordpress/blocks';
import 'antd/dist/reset.css';
import '../assets/sass/tru-fetcher-admin.scss';
import SidebarMetaBoxLoader from "./wp/sidebar/SidebarMetaBoxLoader";

import ListingsBlockEdit from "./wp/blocks/listings/ListingsBlockEdit";
import ListingsBlockSave from "./wp/blocks/listings/ListingsBlockSave";

if (!getPlugin('tf-fetcher-plugin')) {
    registerPlugin( 'tf-metadata-plugin', {
        render: SidebarMetaBoxLoader
    } );
}
