import { Component, OnInit } from '@angular/core';
import {AlertNotif} from '../../alert';
import {globalName} from '../../core/_utils/utils';
import {IpServiceService} from '../../core/_services/ip-service.service';
import {AuthService} from '../../core/_services/auth.service';
import {ActivatedRoute, Router} from '@angular/router';
import {LocalService} from '../../core/_services/storage_services/local.service';

@Component({
  selector: 'app-check-code',
  templateUrl: './check-code.component.html',
  styleUrls: ['./check-code.component.css']
})
export class CheckCodeComponent implements OnInit {
    loading:boolean=false
  constructor(private user_auth_service:AuthService,private local_service:LocalService,private router:Router, private route:ActivatedRoute,private ip:IpServiceService) { }
  user:any;
  ngOnInit(): void {
    window.scroll(0,0);
    // localStorage.setItem("activeSer","")
  }

  resendCode(){
    this.user=this.local_service.getItem(globalName.params)
    console.log(this.user)
    
    this.user_auth_service.resendCode({
        user_id:this.user.user_id,
        ip:this.user.ip
    }).subscribe(
        (res:any)=>{
            this.loading=false;
          if(res.send_code){
            AlertNotif.finish("Code de verification","Code envoyé avec succès. Consulter votre boite mail.","success")
          }else{
            AlertNotif.finish("Code de verification",res.message+" ","error")
          }
          //  this.router.navigate(['/main']);
          //  AlertNotif.finish("Mot de passe oublié","Email envoyé","success")
        },
        (err)=>{
            this.loading=false;
            AlertNotif.finish("Code de verification","Echec d'envoi du code","error")}
    )
    // setTimeout(this.resendCode, 5000);
  }

    codeVerification(value){
        //code, user_id, ip,client_id, client_secret, username, password, authorized_always_id

        var data=this.local_service.getItem(globalName.params);
        data['code']=value.code
        data['authorized_always_id']=value.authorized_always_id==""?false:true
        this.user_auth_service.verifyCode(data).subscribe(
            (res:any)=>{
                var url="";
                console.log('------------1---------------')
                console.log(res)

                if(res.message){
                  AlertNotif.finish("Vérification de code",res.message,"error")
                }else{

                  if(res.user.active){
                    this.loading=false;
                    console.log(res.user)
                    if(res.user.is_portal_admin==true){
                        url=globalName.back_url+'?access_token='+res.access_token+'&email='+res.user.email;

                    }else{
                        this.local_service.setItem(globalName.token,res.access_token)
                        this.local_service.setItem(globalName.current_user,res.user)
                        this.local_service.setItem(globalName.refresh_token,res.refresh_token)
                        this.user_auth_service.setUserLoggedIn(true);
                        
                        url=res.redirect_url+'?access_token='+res.access_token+'&email='+res.user.email;

                    }
                    if( res.user.is_portal_admin==true){
                        console.log(url)
                        window.location.href=url;
                    }else{
                        this.router.navigate(['/home']);
                    }
                }else{
                    AlertNotif.finish("Vérification de code",res.message,"error")
                }
              }
            },
            (err)=>{
                this.loading=false;
                console.log(err)
                AlertNotif.finish("Vérification de code","Echec de connexion","error")}
        )
    }

}
