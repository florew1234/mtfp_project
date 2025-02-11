import { Injectable } from '@angular/core';
import { CanActivate, ActivatedRouteSnapshot, RouterStateSnapshot, UrlTree, Router } from '@angular/router';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class IsAuthGuard implements CanActivate {

  constructor(private router: Router) { }

  canActivate(route: ActivatedRouteSnapshot, state: RouterStateSnapshot) {
    if (localStorage.getItem('mataccueilToken')!=null) {
        // logged in so return true
      this.router.navigate(['/dashboard']);
      return true;
    }
    // If not login user the redirect to login page
    this.router.navigate(['/login-v2']);
    return false;
  }
  
}
