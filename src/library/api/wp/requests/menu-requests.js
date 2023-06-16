import {sendRequest} from "../../state-middleware";

export async function updateMenuItemRolesRequest({
    menuId,
    menuItemId,
    data = {}
}) {
    return await sendRequest({
        method: 'POST',
        endpoint: `menu/${menuId}/items/${menuItemId}/roles/update`,
        data
    });
}
