import { Injectable } from '@angular/core';

import {CanActivate, ActivatedRouteSnapshot, RouterStateSnapshot, UrlTree, Router} from '@angular/router';
import { Observable } from 'rxjs';
import {globalName} from '../_utils/utils';
import { AuthService } from '../_services/auth.service';

@Injectable({
  providedIn: 'root'
})
export class AuthGuardm implements CanActivate {
  constructor(private  router:Router,private user_auth_service:AuthService){}
  canActivate(
    next: ActivatedRouteSnapshot,
    state: RouterStateSnapshot): Observable<boolean | UrlTree> | Promise<boolean | UrlTree> | boolean | UrlTree {
    
      if(localStorage.getItem('matToken')==undefined || localStorage.getItem('matToken')==""){
        this.router.navigate(['/main'])
        return false;
  
        }else{
          this.user_auth_service.setUserLoggedIn(true);
          // if (localStorage.getItem('mataccueilUserData')!=undefined && localStorage.getItem('mataccueilUserData')!=null) {
          //   this.user_auth_service.setUserData(this.localStorage.getJsonValue("mataccueilUserData"));
          // }
          return true;
        }
  }

}
