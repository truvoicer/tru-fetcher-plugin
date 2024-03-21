
import { render } from '@wordpress/element';
import 'antd/dist/reset.css';
import '../assets/sass/tru-fetcher-admin.scss';
import App from "./App";

const element = document.getElementById('tru_fetcher_admin');
console.log(tru_fetcher_react)
if (element) {
    render(<App  apiConfig={tru_fetcher_react?.api?.wp} />, element);
}
