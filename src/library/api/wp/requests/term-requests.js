import {fetchRequest, sendRequest} from "../../state-middleware";

export async function saveTermsRequest(data) {
    console.log({data})
    // return await sendRequest({
    //     method: 'post',
    //     endpoint: 'taxonomy/tr_news_app_categories/terms/save',
    //     data: requestData
    // })
    // console.log({results})
}

export async function fetchTermsRequest() {
    return fetchRequest({
        endpoint: 'taxonomy/tr_news_app_categories/terms'
    });
}

export async function createTermRequest(data) {
    return sendRequest({
        method: 'post',
        endpoint: 'taxonomy/tr_news_app_categories/term/create',
        data
    });
}

export async function updateTermRequest(data) {
    return sendRequest({
        method: 'put',
        endpoint: 'taxonomy/tr_news_app_categories/term/update',
        data
    })
}

export async function deleteTermRequest(data) {
    return sendRequest({
        method: 'delete',
        endpoint: 'taxonomy/tr_news_app_categories/term/delete',
        data
    })
}
