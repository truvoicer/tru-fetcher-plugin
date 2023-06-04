import App from "./App";
import { render } from '@wordpress/element';
import { registerBlockType } from '@wordpress/blocks';

import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls, RichText } from '@wordpress/block-editor';
import { useSelect } from '@wordpress/data';
import { useEntityProp } from '@wordpress/core-data';
import { TextControl, PanelBody, PanelRow } from '@wordpress/components';
// import { registerPlugin } from '@wordpress/plugins';

/**
 * Import the stylesheet for the plugin.
 */

import 'antd/dist/reset.css';
import '../assets/sass/tru-fetcher-admin.scss';
import PostMetaBox from "./wp/post/PostMetaBox";
// import SidebarMetaboxLoader from "./wp/sidebar/SidebarMetaboxLoader";
// render(<App  />, document.getElementById('tru_fetcher_admin'));
console.log('tru_fetcher_react', tru_fetcher_react.currentScreen);

switch (tru_fetcher_react?.currentScreen?.base) {
    case 'post':
        loadByPostScreenId(tru_fetcher_react?.currentScreen?.id)
        break;
    case "toplevel_page_tru-fetcher":
        // Render the App component into the DOM
        render(<App  />, document.getElementById('tru_fetcher_admin'));
        break;
}

function loadByPostScreenId(id) {
    switch (id) {
        case 'post':
        case 'page':
        // registerPlugin( 'metadata-plugin', {
        //     render: SidebarMetaboxLoader
        // } );
            break;
        case 'ft_single_comparison':
            console.log('ft_single_comparison');
            render(<PostMetaBox  />, document.getElementById('trf_single_comparison'));
            break;
    }
}
