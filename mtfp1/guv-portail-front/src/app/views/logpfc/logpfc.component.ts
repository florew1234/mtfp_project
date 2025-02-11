import { Component, OnInit } from '@angular/core';
import {AuthService} from '../../core/_services/auth.service';
import {AlertNotif} from '../../alert';
import {clientData, globalName} from '../../core/_utils/utils';
import {LocalService} from '../../core/_services/storage_services/local.service';
import { Subscription } from 'rxjs';
import { Config } from '../../app.config';
import { Router } from '@angular/router';

@Component({
  selector: 'app-logpfc',
  templateUrl: './logpfc.component.html',
  styleUrls: ['./logpfc.component.css']
})
export class LogpfcComponent implements OnInit {


  subs:Subscription

  constructor(private user_auth_service:AuthService, private  local_service:LocalService,private router:Router) { 
    
    }
  loading:boolean=false
  id:any
  data:any
  user:any
  access_token:any
  error=''
  ngOnInit(): void {
    // 
    localStorage.removeItem("matToken")
    localStorage.removeItem("matUserData")
  }

  loginSend(value) {

    localStorage.removeItem("matToken")
    localStorage.removeItem("matUserData")
    this.loading = true;
    this.user_auth_service.loginpfc(value).subscribe((res:any) => {
        this.loading = false;
        if (res) {
          localStorage.setItem('matToken',res.token);
          this.user_auth_service.getUserByToken(res.token).subscribe((res:any) => {
            localStorage.setItem('matUserData',res);
            this.local_service.setJsonValue('matUserData', res);
          })
          this.router.navigateByUrl("/homepfc"); 
          setTimeout(function(){
            window.location.reload()
          },1000)	
        }
      },err => {
        this.loading = false; 
        if(err.error.error=="invalid_credentials"){
          AlertNotif.finish("Erreur de connexion","Email ou mot de passe incorrect","error")
          // this.error="Email ou mot de passe incorrect"
        }else{
          // this.error="Erreur de connexion ou paramètres incorrects"
          AlertNotif.finish("Erreur de connexion","Erreur de connexion ou paramètres incorrects","error")
        }
      });
  }
  
}
