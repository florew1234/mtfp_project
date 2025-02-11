"use strict";
Object.defineProperty(exports, "__esModule", { value: true });
exports.roles = exports.clientData = exports.globalName = void 0;
var globalName;
(function (globalName) {
    globalName["token"] = "guvToken";
    globalName["current_user"] = "guvUserData";
    globalName["refresh_token"] = "guvRefreshToken";
    globalName["expiredAt"] = "guvTokenExpiredAt";
    globalName["role"] = "role";
})(globalName = exports.globalName || (exports.globalName = {}));
var clientData;
(function (clientData) {
    clientData["client_id"] = "2";
    clientData["client_secret"] = "13T5Zsy7u6jpCjdxGb19nzxBo9idvkdcqr5qrMFF";
    clientData["grant_type"] = "password";
})(clientData = exports.clientData || (exports.clientData = {}));
var roles;
(function (roles) {
    roles["superAdmin"] = "superAdmin";
    roles["admin"] = "admin";
    roles["executor"] = "executor";
    roles["client"] = "client";
})(roles = exports.roles || (exports.roles = {}));
