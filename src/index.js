import App from "./App";
import { render } from '@wordpress/element';


/**
 * Import the stylesheet for the plugin.
 */
import '../assets/sass/tr-news-app-admin.scss';
// Render the App component into the DOM
render(<App  />, document.getElementById('tr_news_app_admin'));
