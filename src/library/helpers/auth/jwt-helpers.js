const CryptoJS = require("crypto-js");

const defaultHeader = {
    "alg": "HS256",
    "typ": "JWT"
};

function base64url(source) {
    let encodedSource;
    // Encode in classical base64
    encodedSource = CryptoJS.enc.Base64.stringify(source);

    // Remove padding equal characters
    encodedSource = encodedSource.replace(/=+$/, '');

    // Replace characters according to base64url specifications
    encodedSource = encodedSource.replace(/\+/g, '-');
    encodedSource = encodedSource.replace(/\//g, '_');

    return encodedSource;
}

function getDataString(data) {
    return CryptoJS.enc.Utf8.parse(JSON.stringify(data));
}

function getEncodedData(dataString) {
    return base64url(dataString);
}

function buildJwt(encodedHeader, encodedData) {
    return `${encodedHeader}.${encodedData}`;
}

function buildSignature(token, secret) {
    const signature = CryptoJS.HmacSHA256(token, secret);
    return base64url(signature);
}

export function getSignedJwt({
    secret,
    payload,
    headerData = defaultHeader
}) {
    const headerString = getDataString(headerData);
    const encodedHeader = getEncodedData(headerString);
    const payloadString = getDataString(payload);
    const encodedPayload = getEncodedData(payloadString);
    const token = buildJwt(encodedHeader, encodedPayload);
    const signature = buildSignature(token, secret);
    return `${token}.${signature}`;
}
