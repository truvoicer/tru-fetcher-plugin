function adminNoticeToggle(status, findAlert) {
    $(findAlert).removeClass(`notice-success`);
    $(findAlert).removeClass(`notice-error`);
    $(findAlert).addClass(`notice-${status}`);
}

function processResponseDataItem(results) {
    let resultList = '<ul>';
    for (let i = 0; i < results.length; i++) {
        const resultItem = results[i];
        if (
            !resultItem.hasOwnProperty('model') ||
            !resultItem.model
        ) {
            continue;
        }
        resultList += '<li>'
        resultList += `<div><span>Table: </span>${resultItem.model}</div>`;

        if (
            !resultItem.hasOwnProperty('result') ||
            !resultItem.result.hasOwnProperty('success')
        ) {
            resultList += `<div><span>Status: </span><span class="dashicons dashicons-dismiss"></span></div>`;
            resultList += `<div><span>Message: </span>Unknown error</div>`;
            resultList += '</li>'
            continue;
        }

        let resultMessage = false;
        if (resultItem.result.hasOwnProperty('message')) {
            resultMessage = resultItem.result.message;
        }
        if (!resultItem.result.success) {
            resultList += `<div><span>Status: </span><span class="dashicons dashicons-dismiss"></span></div>`;
            if (resultMessage) {
                resultList += `<div><span>Message: </span>${resultMessage}</div>`;
            }
        } else {
            resultList += `<div><span>Status: </span><span class="dashicons dashicons-yes-alt"></span></div>`;
        }
        resultList += '</li>'
    }
    resultList += '</ul>'
    return resultList;
}
// tr_news_app_db_update_columns
$(document).on('click', '.tr_news_app_database_install', function (e) {
    e.preventDefault();
    handleDatabaseActions('tr_news_app_database_install_action', this)
})
$(document).on('click', '.tr_news_app_database_network_install', function (e) {
    e.preventDefault();
    handleDatabaseActions('tr_news_app_database_network_install_action', this)
})
$(document).on('click', '.tr_news_app_db_req_data_install', function (e) {
    e.preventDefault();
    handleDatabaseActions('tr_news_app_db_req_data_install', this)
})
$(document).on('click', '.tr_news_app_db_update_columns', function (e) {
    e.preventDefault();
    handleDatabaseActions('tr_news_app_db_update_columns', this)
})
$(document).on('click', '.tr_news_app_db_network_update_columns', function (e) {
    e.preventDefault();
    handleDatabaseActions('tr_news_app_db_network_update_columns', this)
})
$(document).on('click', '.tr_news_app_db_network_req_data_install', function (e) {
    e.preventDefault();
    handleDatabaseActions('tr_news_app_db_network_req_data_install', this)
})

function handleDatabaseActions(action, e) {
    const findAlert = $(e).closest('.tr-news-app-admin-messages');
    $(e).html('Installing...');
    const data = {
        action: action
    };
    $.ajax({
        url: ajaxurl,
        method: "POST",
        data
    })
        .fail(function (data, textStatus, errorThrown) {
            adminNoticeToggle('error', findAlert)
            console.error(data)
        })
        .always(function (data, textStatus, errorThrown) {
            let status = 'error';
            if(
                !data.hasOwnProperty('data') ||
                !data.data.hasOwnProperty('data') ||
                !data.data.hasOwnProperty('code')
            ) {
                adminNoticeToggle(status, findAlert)
                return;
            }

            let responseMessage = false;
            if(data.data.hasOwnProperty('message')) {
                responseMessage = data.data.message;
            }
            let responseCode = data.data.code;
            let results = data.data.data;
            if (typeof results !== 'object') {
                console.error('data format error')
                return;
            }
            switch (responseCode) {
                case 'tr_news_app_db_install_error':
                case 'tr_news_app_db_req_data_install_error':
                    break;
                case 'tr_news_app_db_install_success':
                case 'tr_news_app_db_req_data_install_success':
                    status = 'success';
                    break;
            }
            let resultKeys = Object.keys(results);
            let resultHtml = '';
            for (let a = 0; a < resultKeys.length ; a++) {
                let resultKey = resultKeys[a];
                if (!Array.isArray(results[resultKey])) {
                    console.error('data format error')
                    return;
                }

                resultHtml += processResponseDataItem(results[resultKey]);
            }
            if (findAlert) {
                const alertMessageDisplay = $(findAlert).find('.tr-news-app--messages--display');
                $(alertMessageDisplay).html('');
                if (responseMessage) {
                    $(alertMessageDisplay).append(`<p>${responseMessage}</p>`);
                }
                $(alertMessageDisplay).append(resultHtml);
                $(findAlert).fadeIn()
            }
        })

}