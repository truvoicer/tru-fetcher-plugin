import App from "./App";
import { render } from '@wordpress/element';
import { registerBlockType } from '@wordpress/blocks';

import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls, RichText } from '@wordpress/block-editor';
import { useSelect } from '@wordpress/data';
import { useEntityProp } from '@wordpress/core-data';
import { TextControl, PanelBody, PanelRow } from '@wordpress/components';
import { registerPlugin } from '@wordpress/plugins';

/**
 * Import the stylesheet for the plugin.
 */
import '../assets/sass/tru-fetcher-admin.scss';
import PageOptionsMetaBox from "./wp/sidebar/PageOptionsMetaBox";
// Render the App component into the DOM
// render(<App  />, document.getElementById('tru_fetcher_admin'));
registerPlugin( 'metadata-plugin', {
    render: PageOptionsMetaBox
} );
