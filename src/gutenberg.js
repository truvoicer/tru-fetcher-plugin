import { registerPlugin } from '@wordpress/plugins';
import 'antd/dist/reset.css';
import '../assets/sass/tru-fetcher-admin.scss';
import SidebarMetaBoxLoader from "./wp/sidebar/SidebarMetaBoxLoader";

registerPlugin( 'metadata-plugin', {
    render: SidebarMetaBoxLoader
} );
