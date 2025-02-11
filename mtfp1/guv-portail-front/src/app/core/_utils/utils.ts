export enum globalName {
    token = 'guvToken',
    current_user = 'guvUserData',
    admin_client =  'guvUserIsAdmin',
    params = 'guvUserParams',
    refresh_token = 'guvRefreshToken',
    expiredAt = 'guvTokenExpiredAt',
    role = 'role',
   back_url= 'http://localhost:8000/api',
}

export enum clientData {
    client_id = '9deb342d-af78-4f2d-bff5-d040f72dec34',  
    client_secret = '1asAoe5R8YHTv2peCraL8DbeHgnHsNrvOkZmuICE',
    //client_id = '9477596f-dc98-4430-851f-919d71a87c2f',  
    //client_secret = 'HCKxabI7Potj9RUD994ZoE3E915hTiwK573x0CKu',

    admin_client_id = '9deb342d-af78-4f2d-bff5-d040f72dec34',
    admin_client_secret = '1asAoe5R8YHTv2peCraL8DbeHgnHsNrvOkZmuICE',
    
    admin_http = 'http://localhost:8000/',

    // admin_http = 'https://back.guvmtfp.gouv.bj/',
    grant_type = 'password',
    

    // client_id = '9de70fdf-54ea-4b82-ac5a-8ef400254c4f',
    // admin_client_id = '255be389-b86b-4c20-864b-885d0d0caa76',
    // client_secret = 'MTI8jYpcMWPBfqH98Hz8nJEScCVXhkD7rKKfVjUV',
    // admin_client_secret = '51302fe86d',
    // admin_http = 'https://back.guvmtfp.gouv.bj/',
    // grant_type = 'password',

} 
export enum roles {
    superAdmin = 'superAdmin',
    admin = 'admin',
    executor = 'executor',
    client = 'client'
}