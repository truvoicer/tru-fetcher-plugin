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
import PostBlockEdit from "./wp/blocks/post/PostBlockEdit";
import FormProgressBlockEdit from "./wp/blocks/form-progress/FormProgressBlockEdit";
import WidgetBoardBlockEdit from "./wp/blocks/widget-board/WidgetBoardBlockEdit";
import UserStatsBlockEdit from "./wp/blocks/widgets/user-stats/UserStatsBlockEdit";
import UserSocialBlockEdit from "./wp/blocks/widgets/user-social/UserSocialBlockEdit";
import UserProfileBlockEdit from "./wp/blocks/widgets/user-profile/UserProfileBlockEdit";
import SidebarWidgetBlockEdit from "./wp/blocks/widgets/groups/SidebarWidgetBlockEdit";
import ContentWidgetBlockEdit from "./wp/blocks/widgets/groups/ContentWidgetBlockEdit";
import TabsBlockEdit from "./wp/blocks/tabs-block/TabsBlockEdit";
import ItemViewBlockEdit from "./wp/blocks/item-view/ItemViewBlockEdit";
import SearchBlockEdit from "./wp/blocks/search/SearchBlockEdit";
import {defaultState} from "./library/redux/reducers/app-reducer";
import {APP_API, APP_CURRENT_APP_KEY, APP_NAME} from "./library/redux/constants/app-constants";
import {
    SESSION_API_BASE_URL,
    SESSION_API_URLS,
    SESSION_AUTHENTICATED,
    SESSION_IS_AUTHENTICATING,
    SESSION_USER,
    SESSION_USER_ID,
    SESSION_USER_TOKEN,
    SESSION_USER_TOKEN_EXPIRES_AT
} from "./library/redux/constants/session-constants";

if (!getPlugin('trf-fetcher-plugin')) {
    registerPlugin( 'trf-metadata-plugin', {
        render: SidebarMetaBoxLoader
    } );
}
/**
 * WordPress dependencies
 */

console.log('tru_fetcher_react', tru_fetcher_react);
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
            case "post_block":
                blockComponent = PostBlockEdit;
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
            case "search_block":
                blockComponent = SearchBlockEdit;
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
            reducers: {
                app: {
                    ...defaultState,
                    [APP_NAME]: tru_fetcher_react?.app_name,
                    [APP_API]: tru_fetcher_react.api,
                    [APP_CURRENT_APP_KEY]: tru_fetcher_react.api?.tru_fetcher?.app_key,
                },
                session: {
                    ...defaultState,
                    [SESSION_AUTHENTICATED]: true,
                    [SESSION_IS_AUTHENTICATING]: false,
                    [SESSION_API_URLS]: {
                        [SESSION_API_BASE_URL]: tru_fetcher_react.api?.tru_fetcher?.baseUrl,
                    },
                    [SESSION_USER]: {
                        [SESSION_USER_TOKEN]: tru_fetcher_react.api?.tru_fetcher?.token,
                        [SESSION_USER_TOKEN_EXPIRES_AT]: null,
                        [SESSION_USER_ID]: null,
                    },
                }
            }
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
