import { environment } from '../environments/environment';

import { HttpHeaderResponse, HttpHeaders } from '@angular/common/http';

export const Config: any = {
    //prod
    apiVersion: environment.API_VERSION,
    apiScheme: environment.API_SCHEME,
    apiDomain: environment.API_DOMAIN,
    apiFile: environment.API_FILE,

    toApiUrl(path) {
        return `${this.apiScheme}://${this.apiDomain}/${path}`;
    },
    toFile(path) {
        return `${this.apiScheme}://${this.apiFile}${path}`;
    },
    httpHeader(token = null, isJson = true) {

        if (token != null) {
            return {
                headers: new HttpHeaders({
                    'Authorization': 'Bearer ' + token,
                    'Accept': 'json',
                })

            };
        }
        return {
            headers: new HttpHeaders({})
        };
    },
    toWsUrl(path) {
        return `wss://${this.apiDomain}/${path}`;
    }
};
