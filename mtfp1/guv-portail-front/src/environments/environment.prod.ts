export const environment = {
    production: true,
    isMockEnabled: true, // You have to switch this, when your real back-end is done
    API_SCHEME: 'https',
    API_DOMAIN: 'back.guvmtfp.gouv.bj/api',
    API_FILE: 'back.guvmtfp.gouv.bj',
    API_VERSION: 'v1',
    recaptcha: {
        siteKey: '6Leor0sdAAAAANNhcT-okRExOrfRxP-M0dsFvLAD',
        secret :'6Leor0sdAAAAAEeZn4K4qZvYTRVqrpfJYFz2TJIR'
    },
};

// export const environment = {
//     production: false,
//     isMockEnabled: false, // You have to switch this, when your real back-end is done
//     API_SCHEME: 'http',
//     API_DOMAIN: 'localhost:8001/api',
//     API_FILE: 'localhost:8001',
//     API_VERSION: 'v1',
//     recaptcha: {
//         siteKey: '6Leor0sdAAAAANNhcT-okRExOrfRxP-M0dsFvLAD',
//         secret :'6Leor0sdAAAAAEeZn4K4qZvYTRVqrpfJYFz2TJIR'
//     },
// };