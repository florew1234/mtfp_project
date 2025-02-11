import { Injectable } from '@angular/core';
import {CanActivate, ActivatedRouteSnapshot, RouterStateSnapshot, UrlTree, Router} from '@angular/router';
import { Observable } from 'rxjs';
import {globalName} from '../_utils/utils';

@Injectable({
  providedIn: 'root'
})
export class RegisterguardGuard implements CanActivate {
    constructor(private  router:Router){}
  canActivate(
    next: ActivatedRouteSnapshot,
    state: RouterStateSnapshot): Observable<boolean | UrlTree> | Promise<boolean | UrlTree> | boolean | UrlTree {
      if(localStorage.getItem("is_registered")==undefined){
          this.router.navigate(['/register'])
          return false;
      }else if(localStorage.getItem("is_mail_check")==undefined){
          this.router.navigate(['/main'])
          return false;
      }
      return true;
  }
  
}
