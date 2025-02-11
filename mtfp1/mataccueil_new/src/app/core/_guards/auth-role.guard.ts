import { Injectable } from '@angular/core';
import { CanActivate, ActivatedRouteSnapshot, RouterStateSnapshot, UrlTree, Router } from '@angular/router';
import { Observable } from 'rxjs';
import { AuthentificationService } from '../_services/authentification.service';
import { LocalService } from '../_services/browser-storages/local.service';

@Injectable({
  providedIn: 'root'
})
export class AuthRoleGuard implements CanActivate {
  
  constructor(private router: Router,private authService:AuthentificationService, private localStorageService:LocalService) { }

  canActivate(route: ActivatedRouteSnapshot, state: RouterStateSnapshot) {
    console.log(localStorage.getItem('mataccueilToken'))
    if (localStorage.getItem('mataccueilToken')!=undefined && localStorage.getItem('mataccueilToken')!=null) {
        // logged in so return true
        /*if (localStorage.getItem('mataccueilUserData')!=undefined && localStorage.getItem('mataccueilUserData')!=null) {
          this.authService.setUserData(this.localStorageService.getJsonValue("mataccueilUserData"));
        }*/
        return true;
     }
    // If not login user the redirect to login page
    this.router.navigate(['/login-v2'], { queryParams: { returnUrl: state.url }});
    return false;
}

}
