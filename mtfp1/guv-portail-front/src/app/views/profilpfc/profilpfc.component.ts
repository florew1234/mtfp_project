import { Component, OnInit } from '@angular/core';
import {AuthService} from '../../core/_services/auth.service';
import {AlertNotif} from '../../alert';
import {clientData, globalName} from '../../core/_utils/utils';
import {LocalService} from '../../core/_services/storage_services/local.service';
import { Subscription } from 'rxjs';
import { Config } from '../../app.config';
import { Router } from '@angular/router';

@Component({
  selector: 'app-profilpfc',
  templateUrl: './profilpfc.component.html',
  styleUrls: ['./profilpfc.component.css']
})
export class ProfilpfcComponent implements OnInit {


  subs:Subscription

  constructor(
    private user_auth_service:AuthService, 
    private  local_service:LocalService,
    private localStorageService:LocalService,
    private router:Router) { 
    
    }
  loading:boolean=false
  id:any
  data:any
  user:any
  access_token:any
  error=''
  ngOnInit(): void {
    if (localStorage.getItem('matUserData') != null) {
      this.user = this.localStorageService.getJsonValue("matUserData")
    }
  }

  UpdatePassWord(value){
    
    if (!value.last_password) {
      AlertNotif.finish("Erreur", "Renseigner l'ancien mot de passe", 'error');
    }else if(!value.new_password){
      AlertNotif.finish("Erreur", "Renseigner le nouveau mot de passe", 'error');
    }else if(!value.confirm_password){
      AlertNotif.finish("Erreur", "Confirmer le nouveau mot de passe", 'error');
    }else if(value.confirm_password !== value.new_password){
      AlertNotif.finish("Erreur", "La confirmation n'est pas correcte", 'error');
    }else{
      this.loading=true;
      value['id']=this.user.id;
      this.user_auth_service.resetPasswordpfc(value).subscribe(
          (res:any)=>{
            this.loading=false;
            if(res.success == true){
              this.router.navigate(['/homepfc']);
              AlertNotif.finish("Modification du mot de passe","Nouveau mot de passe pris en compte","success")
            }else{
              AlertNotif.finish("Modification du mot de passe",res.message,"success")
            }
          },
          (err)=>{
              this.loading=false;
              AlertNotif.finish("Modification du mot de passe",err.error.message,"error")}
      )
    }

}
}
