import { Component, OnInit } from '@angular/core';
import {AuthService} from '../../core/_services/auth.service';
import {AlertNotif} from '../../alert';
import {clientData, globalName} from '../../core/_utils/utils';
import {LocalService} from '../../core/_services/storage_services/local.service';
import { Subscription } from 'rxjs';
import { Config } from '../../app.config';
import { Router } from '@angular/router';

@Component({
  selector: 'app-home',
  templateUrl: './home.component.html',
  styleUrls: ['./home.component.css']
})
export class HomeComponent implements OnInit {

  loadFile(file){
    return Config.toFile(file)
  }


  subs:Subscription

  constructor(private user_auth_service:AuthService, private  local_service:LocalService,private router:Router) { 
    if(localStorage.getItem(globalName.current_user)!=undefined) this.user=this.local_service.getItem(globalName.current_user);
    
  }
  loading:boolean=false
  id:any
  data:any
  user:any
  access_token:any

  ngOnInit(): void {
      this.user=this.local_service.getItem(globalName.current_user)
      this.id=this.user.status_id
      this.access_token=this.local_service.getItem(globalName.token)
      this.user_auth_service.getApps(this.id).subscribe(
          (res:any)=>{
                  this.loading=false;
                  this.data=res.data
          },
          (err)=>{
              this.loading=false;
              console.log(err)
              AlertNotif.finish("Applications","Echec de récupération des applications","error")}
      )

      
  }

  setNotif(){
    this.user_auth_service.setNotif({

    }).subscribe(
      (res:any)=>{
             
      },
      (err)=>{
          this.loading=false;
          console.log(err)
         // AlertNotif.finish("Applications","Echec de récupération des applications","error")
        }
  )
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
