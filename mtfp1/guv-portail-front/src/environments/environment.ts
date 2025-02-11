// This file can be replaced during build by using the `fileReplacements` array.
// `ng build --prod` replaces `environment.ts` with `environment.prod.ts`.
// The list of file replacements can be found in `angular.json`.

export const environment = {
    production: false,
    isMockEnabled: true, // You have to switch this, when your real back-end is done
    API_SCHEME: 'http',
    API_DOMAIN: 'localhost:8000/api',
    API_FILE:   'localhost:8000',
    API_VERSION: 'v1',
    recaptcha: {
        siteKey: '6Leor0sdAAAAANNhcT-okRExOrfRxP-M0dsFvLAD',
        secret :'6Leor0sdAAAAAEeZn4K4qZvYTRVqrpfJYFz2TJIR'
    },
};

/*
 * For easier debugging in development mode, you can import the following file
 * to ignore zone related error stack frames such as `zone.run`, `zoneDelegate.invokeTask`.
 *
 * This import should be commented out in production mode because it will have a negative impact
 * on performance if an error is thrown.
 */
// import 'zone.js/plugins/zone-error';  // Included with Angular CLI.
