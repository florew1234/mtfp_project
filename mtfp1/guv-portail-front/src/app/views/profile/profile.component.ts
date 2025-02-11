import { Component, OnInit } from '@angular/core';
import {globalName} from '../../core/_utils/utils';
import {LocalService} from '../../core/_services/storage_services/local.service';
import {StatusService} from '../../core/_services/status.service';
import {AlertNotif} from '../../alert';
import {Router} from '@angular/router';
import {AuthService} from '../../core/_services/auth.service';
import { Subscription } from 'rxjs';

@Component({
  selector: 'app-profile',
  templateUrl: './profile.component.html',
  styleUrls: ['./profile.component.css']
})
export class ProfileComponent implements OnInit {

  user:any
    isAgent:boolean=false
    needIfu:boolean=false
    status:any
    loading:boolean=false
    isLogin:boolean=false

    subs:Subscription

  constructor(private local_service:LocalService, private status_service:StatusService, private router:Router, private user_auth_service:AuthService) { 

    if(localStorage.getItem(globalName.current_user)!=undefined) this.user=this.local_service.getItem(globalName.current_user);
        this.subs=this.user_auth_service.getUserLoggedIn().subscribe(res => {
            this.isLogin = res
        });
  }

  ngOnInit(): void {
      this.user=this.local_service.getItem(globalName.current_user)
      this.status_service.getAll().subscribe(
          (res:any)=>{
              this.status=res.data;

          })
  }

    update(value){
      this.loading=true;
      console.log(value);
      value['id']=this.user.id;
      this.user_auth_service.update(value).subscribe(
          (res:any)=>{
              this.loading=false;
              this.router.navigate(['/home']);
              AlertNotif.finish("Mise à jour profil","Mise à jour effectuée avec succès","success")
          },
          (err)=>{
              this.loading=false;

              AlertNotif.finish("Mise à jour profil","Mise à jour effectuée avec succès","error")}
      )
    }

    resetPassword(value){
        this.loading=true;
        console.log(value);
        value['id']=this.user.id;

        this.user_auth_service.resetPassword(value).subscribe(
            (res:any)=>{
                this.loading=false;
                this.router.navigate(['/home']);
                AlertNotif.finish("Modification du mot de passe","Nouveau mot de passe pris en compte","success")
            },
            (err)=>{
                this.loading=false;

                AlertNotif.finish("Modification du mot de passe","Echec de réinitialisation","error")}
        )

    }
}
