import UserStats from "../components/user-stats/UserStats";
import UserSocial from "../components/user-social/UserSocial";
import UserProfile from "../components/user-profile/UserProfile";
import FormProgress from "../components/form-progress/FormProgress";

export default [
    {
        id: 'user-stats',
        title: 'User Stats',
        component: UserStats,
    },
    {
        id: 'user-social',
        title: 'User Social',
        component: UserSocial,
    },
    {
        id: 'user-profile',
        title: 'User Profile',
        component: UserProfile,
    },
    {
        id: 'form-progress',
        title: 'Form Progress',
        component: FormProgress,
    }

]
