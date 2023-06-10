import { render } from '@wordpress/element';
import 'antd/dist/reset.css';
import '../assets/sass/tru-fetcher-admin.scss';
import SingleItemMetaBoxTabs from "./wp/post/meta-boxes/single-item/SingleItemMetaBoxTabs";
import ItemListMetaBoxList from "./wp/post/meta-boxes/item-list/ItemListMetaBoxList";
import MetaBoxContainer from "./wp/post/meta-boxes/MetaBoxContainer";
console.log('tru_fetcher_react', tru_fetcher_react);
switch (tru_fetcher_react?.currentScreen?.base) {
    case 'post':
        loadByPostScreenId(tru_fetcher_react?.currentScreen?.id)
        break;
}

function loadByPostScreenId(id) {
    let element;
    switch (id) {
        case 'fetcher_single_item':
            element = document.getElementById('trf_mb_single_item_react');
            if (!element) {
                return;
            }
            SingleItemMetaBoxTabs.defaultProps = {
                config: tru_fetcher_react?.api?.tru_fetcher
            };
            render(<MetaBoxContainer metaBoxComponent={SingleItemMetaBoxTabs}  />, element);
            break;
        case 'fetcher_items_lists':
            element = document.getElementById('trf_mb_item_list_react');
            if (!element) {
                return;
            }
            ItemListMetaBoxList.defaultProps = {
                config: tru_fetcher_react?.api?.wp
            };
            render(<MetaBoxContainer metaBoxComponent={ItemListMetaBoxList}  />, element);
            break;
    }
}
