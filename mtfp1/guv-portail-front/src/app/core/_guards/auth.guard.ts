import { Injectable } from '@angular/core';

import {CanActivate, ActivatedRouteSnapshot, RouterStateSnapshot, UrlTree, Router} from '@angular/router';
import { Observable } from 'rxjs';
import {globalName} from '../_utils/utils';
import { AuthService } from '../_services/auth.service';

@Injectable({
  providedIn: 'root'
})
export class AuthGuard implements CanActivate {
  constructor(private  router:Router,private user_auth_service:AuthService){}
  canActivate(
    next: ActivatedRouteSnapshot,
    state: RouterStateSnapshot): Observable<boolean | UrlTree> | Promise<boolean | UrlTree> | boolean | UrlTree {
    if(localStorage.getItem(globalName.token)==undefined || localStorage.getItem(globalName.token)==""){
      this.router.navigate(['/main'])
      return false;

    }else {
      if(localStorage.getItem(globalName.admin_client)=="1" || localStorage.getItem(globalName.admin_client)=="true"){
        this.router.navigate(['/main'])
        return false;
      }else{
        this.user_auth_service.setUserLoggedIn(true);
        return true;
      }
    }
  }

}
