import React from 'react';
import {Provider} from "react-redux";
import store from "../../library/redux/store";
import PostMetaBoxTabs from "./PostMetaBoxTabs";


const PostMetaBox = () => {
    return (
        <Provider store={store}>
            <PostMetaBoxTabs />
        </Provider>
    );
}

export default PostMetaBox;
