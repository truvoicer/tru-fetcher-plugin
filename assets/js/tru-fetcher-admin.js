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
// tru_fetcher_db_update_columns
$(document).on('click', '.tru_fetcher_database_install', function (e) {
    e.preventDefault();
    handleDatabaseActions('tru_fetcher_database_install_action', {}, this)
})
$(document).on('click', '.tru_fetcher_database_network_install', function (e) {
    e.preventDefault();
    handleDatabaseActions('tru_fetcher_database_network_install_action', {}, this)
})
$(document).on('click', '.tru_fetcher_db_req_data_install', function (e) {
    e.preventDefault();
    const dataModelsAttr = $(this).attr('data-models');
    let data = {};
    if (typeof dataModelsAttr !== 'undefined' && dataModelsAttr) {
        data.models = dataModelsAttr.split(',')
    }
    handleDatabaseActions('tru_fetcher_db_req_data_install', data, this)
})
$(document).on('click', '.tru_fetcher_db_update_columns', function (e) {
    e.preventDefault();
    handleDatabaseActions('tru_fetcher_db_update_columns', {}, this)
})
$(document).on('click', '.tru_fetcher_db_network_update_columns', function (e) {
    e.preventDefault();
    handleDatabaseActions('tru_fetcher_db_network_update_columns', {}, this)
})
$(document).on('click', '.tru_fetcher_db_network_req_data_install', function (e) {
    e.preventDefault();
    handleDatabaseActions('tru_fetcher_db_network_req_data_install', {}, this)
})

function handleDatabaseActions(action, extraData = {}, e) {
    const findAlert = $(e).closest('.tru-fetcher-admin-messages');
    $(e).html('Installing...');
    const data = {
        action: action
    };
    $.ajax({
        url: ajaxurl,
        method: "POST",
        data: {...data, ...extraData}
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
            if (responseCode.includes('_success')) {
                status = 'success';
            }
            adminNoticeToggle(status, findAlert)
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
                const alertMessageDisplay = $(findAlert).find('.tru-fetcher--messages--display');
                $(alertMessageDisplay).html('');
                if (responseMessage) {
                    $(alertMessageDisplay).append(`<p>${responseMessage}</p>`);
                }
                $(alertMessageDisplay).append(resultHtml);
                $(findAlert).fadeIn()
            }
        })

}
