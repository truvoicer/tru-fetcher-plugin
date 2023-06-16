import {fetchRequest} from "../../state-middleware";

export async function fetchDevicesRequest() {
    const results = await fetchRequest({
        endpoint: 'firebase/devices'
    });

    const devicesRes = results?.data?.devices;
    if (!Array.isArray(devicesRes)) {
        console.error('Devices invalid response')
        return false;
    }
    return devicesRes;
}

export function getDevicesSelectData(devices) {
    return devices.map(device => {
        return {
            label: device?.id,
            value: device?.id
        };
    });
}
