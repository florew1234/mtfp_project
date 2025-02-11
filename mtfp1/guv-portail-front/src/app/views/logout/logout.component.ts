import { Component, OnInit } from '@angular/core';
import {globalName} from '../../core/_utils/utils';
import {AuthService} from '../../core/_services/auth.service';
import {Router} from '@angular/router';

@Component({
  selector: 'app-logout',
  templateUrl: './logout.component.html',
  styleUrls: ['./logout.component.css']
})
export class LogoutComponent implements OnInit {

  constructor(private user_auth_service:AuthService, private router:Router) {
    this.logout();
  }

  ngOnInit(): void {
  }
    logout(){
        localStorage.removeItem(globalName.token);
        localStorage.removeItem(globalName.current_user);

        this.user_auth_service.setUserLoggedIn(false)
        this.router.navigate(['/main']);
        /* this.user_auth_service.logout().subscribe(
             (res:any)=>{
             },
             (err)=>{
                 AlertNotif.finish("Déconnexion","Echec de déconnexion","error")}
         )*/

    }
}
