import UserStats from "../components/user-stats/UserStats";
import UserSocial from "../components/user-social/UserSocial";
import UserProfile from "../components/user-profile/UserProfile";
import FormProgress from "../components/form-progress/FormProgress";
import Tabs from "../components/tabs/Tabs";
import SavedItems from "../components/saved-items/SavedItems";

export default [
    {
        id: 'user_stats_widget_block',
        title: 'User Stats',
        component: UserStats,
    },
    {
        id: 'user_social_widget_block',
        title: 'User Social',
        component: UserSocial,
    },
    {
        id: 'user_profile_widget_block',
        title: 'User Profile',
        component: UserProfile,
    },
    {
        id: 'form_progress_widget_block',
        title: 'Form Progress',
        component: FormProgress,
    },
    {
        id: 'tabs_block',
        block_id: 'tabs_block',
        title: 'Tabs',
        component: Tabs,
    },
    {
        id: 'saved_items_widget_block',
        title: 'Saved Items',
        component: SavedItems,
    }

]
