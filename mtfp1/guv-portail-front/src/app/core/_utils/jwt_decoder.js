"use strict";
Object.defineProperty(exports, "__esModule", { value: true });
exports.isExpired = void 0;
var angular_jwt_1 = require("@auth0/angular-jwt");
var utils_1 = require("./utils");
var helper = new angular_jwt_1.JwtHelperService();
var decodedToken = helper.decodeToken(utils_1.globalName.token);
var expirationDate = helper.getTokenExpirationDate(utils_1.globalName.token);
exports.isExpired = helper.isTokenExpired(utils_1.globalName.token);
