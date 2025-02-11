import { Injectable } from '@angular/core';
import { CanActivate, ActivatedRouteSnapshot, RouterStateSnapshot, UrlTree, Router } from '@angular/router';
import { Observable } from 'rxjs';
import { Roles } from '../_models/roles';
// import {AuthentificationService} from './../../beraboo/_services/authentification.service';
import { AuthentificationService } from '../_services/authentification.service';
import { User } from '../_models/user.model';


@Injectable({
  providedIn: 'root'
})
export class AdminRoleGuard implements CanActivate {

  constructor(private router: Router, private authService:AuthentificationService) { 

  }

  canActivate(
    next: ActivatedRouteSnapshot,
    state: RouterStateSnapshot): Observable<boolean | UrlTree> | Promise<boolean | UrlTree> | boolean | UrlTree {
        let role=""
        this.authService.getUserByToken().subscribe((res:any)=>{
          role=res.role;
          if( role == Roles.Admin ){
            // logged in so return true
            return true;
          }      // If not login user the redirect to login page
          this.router.navigate(['/unauthorized']);
          return false;
        });
      
        this.router.navigate(['/unauthorized']);
        return false;
      }
  
  
}
