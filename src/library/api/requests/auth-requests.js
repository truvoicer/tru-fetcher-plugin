import {fetchRequest, sendRequest} from "../middleware";

export async function fetchRolesRequest() {
    const results = await fetchRequest({
        endpoint: 'auth/roles'
    });

    const rolesRes = results?.data?.roles;
    if (!Array.isArray(rolesRes)) {
        console.error('Roles invalid response')
        return false;
    }
    return rolesRes;
}

export function getRolesSelectData(roles) {
    return roles.map(role => {
        return {
            label: role?.label,
            value: role?.name
        };
    });
}
