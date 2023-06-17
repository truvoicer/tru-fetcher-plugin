
import { registerPlugin, getPlugin } from '@wordpress/plugins';
import 'antd/dist/reset.css';
import '../assets/sass/tru-fetcher-admin.scss';
import SidebarMetaBoxLoader from "./wp/sidebar/SidebarMetaBoxLoader";

if (!getPlugin('tf-fetcher-plugin')) {
    registerPlugin( 'tf-metadata-plugin', {
        render: SidebarMetaBoxLoader
    } );
}
