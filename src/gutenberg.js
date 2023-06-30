import { registerPlugin, getPlugin } from '@wordpress/plugins';
import { registerBlockType } from '@wordpress/blocks';
import { useeffect } from '@wordpress/element';
import 'antd/dist/reset.css';
import '../assets/sass/tru-fetcher-admin.scss';
import SidebarMetaBoxLoader from "./wp/sidebar/SidebarMetaBoxLoader";

import ListingsBlockEdit from "./wp/blocks/listings/ListingsBlockEdit";
import HeroBlockEdit from "./wp/blocks/hero/HeroBlockEdit";
import UserAccountBlockEdit from "./wp/blocks/user-account/UserAccountBlockEdit";
import FormBlockEdit from "./wp/blocks/form/FormBlockEdit";
import CarouselBlockEdit from "./wp/blocks/carousel/CarouselBlockEdit";
import OptInBlockEdit from "./wp/blocks/opt-in/OptInBlockEdit";
import PostsBlockEdit from "./wp/blocks/posts/PostsBlockEdit";
import FormProgressBlockEdit from "./wp/blocks/form-progress/FormProgressBlockEdit";
import WidgetBoardBlockEdit from "./wp/blocks/widget-board/WidgetBoardBlockEdit";
import UserStatsBlockEdit from "./wp/blocks/widgets/user-stats/UserStatsBlockEdit";
import UserSocialBlockEdit from "./wp/blocks/widgets/user-social/UserSocialBlockEdit";
import UserProfileBlockEdit from "./wp/blocks/widgets/user-profile/UserProfileBlockEdit";
import SidebarWidgetBlockEdit from "./wp/blocks/widgets/groups/SidebarWidgetBlockEdit";
import ContentWidgetBlockEdit from "./wp/blocks/widgets/groups/ContentWidgetBlockEdit";
import TabsBlockEdit from "./wp/blocks/tabs-block/TabsBlockEdit";
import ItemViewBlockEdit from "./wp/blocks/item-view/ItemViewBlockEdit";

console.log(tru_fetcher_react)
if (!getPlugin('trf-fetcher-plugin')) {
    registerPlugin( 'trf-metadata-plugin', {
        render: SidebarMetaBoxLoader
    } );
}
/**
 * WordPress dependencies
 */


// Export this so we can use it in the edit and save files
export const blockStyle = {
    backgroundColor: '#900',
    color: '#fff',
    padding: '20px',
};
if (
    typeof tru_fetcher_react !== 'undefined' &&
    typeof tru_fetcher_react.blocks !== 'undefined' &&
    Array.isArray(tru_fetcher_react.blocks)
) {
    tru_fetcher_react.blocks.forEach((block) => {
        let attData = {};
        let examplesAttData = {};
        if (typeof block.attributes !== 'undefined' && Array.isArray(block.attributes)) {
            block.attributes.forEach((attribute) => {
                attData[attribute.id] = {
                    type: attribute.type,
                    default: attribute.default,
                };
                examplesAttData[attribute.id] = attribute.default;
            });
        }
        let blockComponent;
        switch (block?.id) {
            case "listings_block":
                blockComponent = ListingsBlockEdit;
                break;
            case "hero_block":
                blockComponent = HeroBlockEdit;
                break;
            case "user_account_block":
                blockComponent = UserAccountBlockEdit;
                break;
            case "form_block":
                blockComponent = FormBlockEdit;
                break;
            case "carousel_block":
                blockComponent = CarouselBlockEdit;
                break;
            case "opt_in_block":
                blockComponent = OptInBlockEdit;
                break;
            case "posts_block":
                blockComponent = PostsBlockEdit;
                break;
            case "form_progress_widget_block":
                blockComponent = FormProgressBlockEdit;
                break;
            case "user_stats_widget_block":
                blockComponent = UserStatsBlockEdit;
                break;
            case "user_social_widget_block":
                blockComponent = UserSocialBlockEdit;
                break;
            case "user_profile_widget_block":
                blockComponent = UserProfileBlockEdit;
                break;
            case "widget_board_block":
                blockComponent = WidgetBoardBlockEdit;
                break;
            case "tabs_block":
                blockComponent = TabsBlockEdit;
                break;
            case "item_view_block":
                blockComponent = ItemViewBlockEdit;
                break;
            // case "sidebar_widgets_block":
            //     blockComponent = SidebarWidgetBlockEdit;
            //     break;
            // case "content_widgets_block":
            //     blockComponent = ContentWidgetBlockEdit;
            //     break;
            default:
                return;
        }
        blockComponent.defaultProps = {
            config: block,
            apiConfig: tru_fetcher_react.api,
        }
        let blockOptions = {};
        blockOptions.title = block.title;
        blockOptions.attributes = attData;
        blockOptions.example = {
            attributes: examplesAttData
        };
        blockOptions.edit = blockComponent;
        if (Array.isArray(block?.parent)) {
            blockOptions.parent = block.parent;
        }
        if (Array.isArray(block?.ancestor)) {
            blockOptions.ancestor = block.ancestor;
        }
        registerBlockType( block.name, blockOptions );
    });
}
