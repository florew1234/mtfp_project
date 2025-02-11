import { Component, OnInit } from '@angular/core';
import { ActivatedRoute } from '@angular/router';
import { AuthentificationService } from '../../../../core/_services/authentification.service';
import { AlertNotif } from '../../../../alert';
import {TranslateService} from '@ngx-translate/core';


@Component({
  selector: 'app-reset-password',
  templateUrl: './reset-password.component.html',
  styleUrls: ['./reset-password.component.css']
})
export class ResetPasswordComponent implements OnInit {

  token=""
  error=""
  loading=false
  constructor(private translateService: TranslateService,private activatedRoute:ActivatedRoute,private authService:AuthentificationService) { }

  ngOnInit(): void {
    let lang=this.activatedRoute.snapshot.paramMap.get('lang');
    this.translateService.use(lang);
    this.token=this.activatedRoute.snapshot.paramMap.get('token');
  }

  submit(value){
    this.loading=true
    if(value.password_c==value.password){
      this.loading=false

      this.authService.resetPassword(value.password,this.token).subscribe((res)=>{
        AlertNotif.finish('Felicitation', 'Votre mot de passe a été mise à jour avec succès', 'success')
         window.location.reload();
      },(err)=>{   
        this.loading=false
         AlertNotif.finish('Erreur', 'Une erreur est survenue, verifier votre connexion internet puis reessayer', 'error')})
    }else{
      this.loading=false
      this.error="les deux mot de passe ne sont pas  identiques"
    }
     
  }

}
