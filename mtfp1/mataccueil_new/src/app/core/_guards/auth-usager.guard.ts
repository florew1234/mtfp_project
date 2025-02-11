import { Injectable } from '@angular/core';
import { CanActivate, ActivatedRouteSnapshot, RouterStateSnapshot, UrlTree, Router, ActivatedRoute } from '@angular/router';
import { Observable } from 'rxjs';
import { AuthentificationService } from '../_services/authentification.service';
import { LocalService } from '../_services/browser-storages/local.service';
import { JwtHelperService } from '@auth0/angular-jwt';



@Injectable({
  providedIn: 'root'
})
export class AuthUsagerGuard implements CanActivate {

  constructor(private activatedRoute: ActivatedRoute, private jwtHelper: JwtHelperService, private router: Router, private auth: AuthentificationService, private localStorageService: LocalService) {


  }
  url="https://demarchesmtfp.gouv.bj?client_id=26d9d6be-d676-465f-b92c-369b72442c7f&client_secret=f5034b6c80a13d411fa03a8d1f14"
  // url="http://portailmtfp.hebergeappli.bj?client_id=26d9d6be-d676-465f-b92c-369b72442c7f&client_secret=f5034b6c80a13d411fa03a8d1f14"

  async  canActivate(route: ActivatedRouteSnapshot, state: RouterStateSnapshot) {


    //verifier validité token   
    const token = route.queryParams.access_token;
    const email = route.queryParams.email;
    if (token != null || token != undefined) {
      localStorage.setItem('guvUserToken', token)
      if (email != null && email != undefined) {
        let resGuv:any={success:false}
       resGuv = await this.auth.getUserSinceGuv("email", email).toPromise()
        
        if (resGuv.success) {
          this.localStorageService.setJsonValue('guvUserData', resGuv.data)
          let res: any ={}
          res = await this.auth.loginUsager(this.localStorageService.getJsonValue('guvUserData')).toPromise()
          
          if (res!=null) {
            console.log("ressssssssssssssssssssssssss")
            await this.localStorageService.setJsonValue('mataccueilUserData', res);
            return true;
          } else {
            localStorage.removeItem('mataccueilUserData')
            window.location.href =this.url;
            return false
          }
        } else {
          localStorage.removeItem('guvUserData')
          window.location.href =this.url;
          return false
        }
      } else {
        window.location.href =this.url;
        return false
      }
    } else {
  
      if(localStorage.getItem('guvUserToken')==null){
        this.redirect()
        return false
      }else{
        if(!this.jwtHelper.isTokenExpired(localStorage.getItem('guvUserToken'))){
          if (localStorage.getItem('mataccueilUserData')!=null) {
            return true
          }else{
            this.redirect()
            return false;
          }
        }else{
          this.redirect()
          return false;
        }
      }
    }
    // If not login user the redirect to login page
    // this.router.navigate(['/login'], { queryParams: { returnUrl: state.url }});
    //return false;
  }
  redirect(){
//       localStorage.removeItem('guvUserToken')
//        localStorage.removeItem('guvUserData')
 //       localStorage.removeItem('mataccueilUserData')
   //     window.location.href =this.url;
  }

}
