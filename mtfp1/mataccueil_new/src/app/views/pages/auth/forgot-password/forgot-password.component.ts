import { Component, OnInit } from '@angular/core';
import { AlertNotif } from '../../../../alert';
import { AuthentificationService } from '../../../../core/_services/authentification.service';
import {TranslateService} from '@ngx-translate/core';

import { ActivatedRoute, Router } from '@angular/router';

@Component({
  selector: 'app-forgot-password',
  templateUrl: './forgot-password.component.html',
  styleUrls: ['./forgot-password.component.css']
})
export class ForgotPasswordComponent implements OnInit {

  error=""
  loading=false
  constructor(private activatedRoute:ActivatedRoute,private router:Router,private translateService: TranslateService,private authService:AuthentificationService) { }
  lang="fr"
  ngOnInit(): void {
    this.lang=this.activatedRoute.snapshot.paramMap.get('lang');
    if(this.lang=="fr" || this.lang=="en"){
      this.translateService.use(this.lang);
    }else{
     
      this.router.navigateByUrl('/forgot-password');
    }
    if(this.lang==null){
      this.lang="fr"
      this.translateService.use("fr");
    }
  }

  submit(value){
    this.loading=true
      this.authService.forgotPassword(value.eamil).subscribe((res)=>{
        this.loading=false
        AlertNotif.finish('Informations', 'Votre processus de reinitialisation de mot de passe a bien été enclencher. Veuillez consulter votre adresse E-mail continuer', 'success')
         window.location.reload();
      },(err)=>{  
        this.loading=false
        AlertNotif.finish('Erreur', 'Une erreur est survenue, verifier votre connexion internet puis reessayer', 'error')})
     
  }

}
