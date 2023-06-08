import { render } from '@wordpress/element';
import 'antd/dist/reset.css';
import '../assets/sass/tru-fetcher-admin.scss';
import SingleItemMetaBox from "./wp/post/MetaBoxes/single-item/SingleItemMetaBox";
import ItemListMetaBox from "./wp/post/MetaBoxes/item-list/ItemListMetaBox";

switch (tru_fetcher_react?.currentScreen?.base) {
    case 'post':
        loadByPostScreenId(tru_fetcher_react?.currentScreen?.id)
        break;
}

function loadByPostScreenId(id) {
    switch (id) {
        case 'fetcher_single_item':
            render(<SingleItemMetaBox  />, document.getElementById('trf_mb_single_item_react'));
            break;
        case 'fetcher_items_lists':
            render(<ItemListMetaBox  />, document.getElementById('trf_mb_item_list_react'));
            break;
    }
}
