import {fetchRequest} from "../../middleware";

export async function fetchTopicsRequest() {
    const results = await fetchRequest({
        endpoint: 'firebase/topics'
    });

    const topicsRes = results?.data?.topics;
    if (!Array.isArray(topicsRes)) {
        console.error('Topics invalid response')
        return false;
    }
    return topicsRes;
}

export function getTopicsSelectData(topics) {
    return topics.map(topic => {
        return {
            label: topic?.topic_name || topic?.id,
            value: topic?.id
        };
    });
}
