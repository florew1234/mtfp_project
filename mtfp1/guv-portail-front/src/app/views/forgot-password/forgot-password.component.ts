import { Component, OnInit } from '@angular/core';
import {LocalService} from '../../core/_services/storage_services/local.service';
import {Router} from '@angular/router';
import {StatusService} from '../../core/_services/status.service';
import {AuthService} from '../../core/_services/auth.service';
import {AlertNotif} from '../../alert';
import {globalName} from '../../core/_utils/utils';

@Component({
  selector: 'app-forgot-password',
  templateUrl: './forgot-password.component.html',
  styleUrls: ['./forgot-password.component.css']
})
export class ForgotPasswordComponent implements OnInit {
    loading:boolean=false;
    constructor(private user_auth_service:AuthService,private router:Router) { }


    ngOnInit(): void {
        window.scroll(0,0);
  }
  gotoHashtag(fragment: string) {
     
    setTimeout(function(){
      const element:any = document.querySelector("#" + fragment);
      if (element) element.scrollIntoView();
    })
}

    forgotPassword(value){
        this.loading=true;
        console.log(value);

        this.user_auth_service.forgotPassword(value).subscribe(
            (res:any)=>{
                this.loading=false;
                this.router.navigate(['/main']);
                AlertNotif.finish("Mot de passe oublié","Un mail vous a été envoyé. Veuillez consulter votre boîte mail","success")
            },
            (err)=>{
            this.loading=false;
            console.log(err)
            if(err.error.message=="Reset password failed.")
            {
                AlertNotif.finish("Mot de passe oublié","Le mail renseigné n'existe pas. Veuillez vérifier le mail puis réessayer","error")
            }else{
                AlertNotif.finish("Mot de passe oublié","Echec de réinitialisation du mot de passe","error")
            }
        }
    )

    }
}
