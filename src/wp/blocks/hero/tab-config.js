import DataTab from "./tabs/DataTab";
import ImageryTab from "./tabs/ImageryTab";
import SearchTab from "./tabs/SearchTab";
import ExtraDataTab from "./tabs/ExtraDataTab";

export default [
    {
        name: 'imagery',
        title: 'Imagery',
        component: ImageryTab
    },
    {
        name: 'data',
        title: 'Data',
        component: DataTab
    },
    {
        name: 'search',
        title: 'Search',
        component: SearchTab
    },
    {
        name: 'extra_data',
        title: 'Extra Data',
        component: ExtraDataTab
    },
]
