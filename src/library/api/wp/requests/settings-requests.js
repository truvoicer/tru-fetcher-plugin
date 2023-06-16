import {fetchRequest} from "../../state-middleware";

export async function fetchTermsRequest() {
    return fetchRequest({
        endpoint: 'taxonomy/tr_news_app_categories/terms'
    });
}
