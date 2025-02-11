export const PORTAL_CONFIG: any = {
    scheme: 'https',
    domain: 'pprodofficial.service-public.bj/official/login',
    client_id: 'app-mtfp',
    redirect_uri: 'api.mataccueil.gouv.bj',
    response_type: 'code',
    scope: 'openid',
    authError: 'true',
    getRedirectPprodUri() {

        return `${this.scheme}://${this.domain}/?client_id=${this.client_id}&redirect_uri=${this.scheme}://${this.redirect_uri}&response_type=${this.response_type}&scope=${this.scope}&authError=${this.authError}`;
      },
    }