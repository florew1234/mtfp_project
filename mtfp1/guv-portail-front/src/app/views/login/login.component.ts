import { Component, OnInit } from '@angular/core';
import {AuthService} from "../../core/_services/auth.service";
import {AlertNotif} from "../../alert";
import {clientData, globalName} from '../../core/_utils/utils';
import {LocalService} from "../../core/_services/storage_services/local.service";
import {ActivatedRoute, Router} from '@angular/router';
import {unsupported} from '@angular/compiler/src/render3/view/util';
import {IpServiceService} from '../../core/_services/ip-service.service';
import {ApplicationService} from '../../core/_services/application.service';
//import {isExpired} from '../../core/_utils/jwt_decoder';

import { OwlOptions } from 'ngx-owl-carousel-o';
import {Config} from '../../app.config';
import Swal from 'sweetalert2'


@Component({
  selector: 'app-login',
  templateUrl: './login.component.html',
  styleUrls: ['./login.component.css']
})
export class LoginComponent implements OnInit {

    active=1
    loadFile(file){
        return Config.toFile(file)
    }

     confirmation(service)
    {
        Swal.fire({
            text: "Inscrivez-vous et authentifiez-vous pour acceder au service "+service,
            icon: 'warning',
            title: 'Service en ligne',
            showCancelButton: true,
            confirmButtonText: 'Poursuivre',
            cancelButtonText: 'Annuler',
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            }).then((result) => {
                if (result.isConfirmed) {
                 location.reload()
                }else{
                    result.dismiss === Swal.DismissReason.cancel
                }
            }, function (dismiss) {
                return false;
            })
    }

    customOptions: OwlOptions = {
        loop:true,
        margin:10,
        nav:false,
        autoplay:true,
        //autoplayTimeout:1000,
        //autoplayHoverPause:true
        responsive:{
            0:{
                items:1
            },
            600:{
                items:3
            },
            1000:{
                items:4
            }
        }
      }
  loading: boolean;
  visitor:boolean=false
  client_id:string;
  client_secret:string;
  needMailCheck:boolean=false
  email:string

    ipAddress:string

  constructor(private applicationService:ApplicationService,private user_auth_service:AuthService,private local_service:LocalService,private router:Router, private route:ActivatedRoute,private ip:IpServiceService) {

      if(this.route.snapshot.paramMap.get('client_id') && this.route.snapshot.paramMap.get('client_secret')){
          this.visitor=true;
          this.client_id=this.route.snapshot.paramMap.get('client_id')
          this.client_secret=this.route.snapshot.paramMap.get('client_secret')
      }
     /* if(!isExpired){
          this.refreshTokenAndRedirect();
      }*/
  }

    getIP()
    {
        this.ip.getIPAddress().subscribe((res:any)=>{
            this.ipAddress=res.ip;
        });
    }

    applications:any[]=[]

    ngOnInit(): void {
      this.getIP();
      if(localStorage.getItem(globalName.current_user)!=undefined) {
        this.router.navigateByUrl('/home')
      }
      this.applications=[]
      this.applicationService.getAll().subscribe((res:any)=>{
        this.applications=res.data
    });
  }


    loginSend(value){
        this.loading=true;
        if(this.visitor){
            value['client_id']=this.client_id;
            value['grant_type']=clientData.grant_type;
            value['client_secret']=this.client_secret;
        }else{
            value['client_id']=clientData.client_id;
            value['grant_type']=clientData.grant_type;
            value['client_secret']=clientData.client_secret;
        }
        this.email=value['email'];
       value['ip']=this.ipAddress;
        this.user_auth_service.login(value).subscribe(
            (res:any)=>{
                if(res.check_code){
                    var data=res.params;
                    data['client_id']=value['client_id']
                    data['grant_type']=value['grant_type']
                    data['client_secret']=value['client_secret']
                    data['user_id']=res.user_id
                    this.local_service.setItem(globalName.params,data)
                    this.router.navigate(['/check-code']);
                }else{
                    var url="";


                    if(res.user.active){
                        this.loading=false;
                       
                        console.log(res.user)
                        if(res.user.is_portal_admin==true){
                            url=globalName.back_url+'?access_token='+res.access_token+'&email='+res.user.email;

                        }else{
                            this.local_service.setItem(globalName.token,res.access_token)
                            this.local_service.setItem(globalName.current_user,res.user)
                            localStorage.setItem(globalName.admin_client,res.user.is_portal_admin)
                            this.local_service.setItem(globalName.refresh_token,res.refresh_token)
                            this.user_auth_service.setUserLoggedIn(true);

                            url=res.redirect_url+'?access_token='+res.access_token+'&email='+res.user.email;

                        }
                        if(this.visitor || res.user.is_portal_admin==true){
                            console.log(url)
                            window.location.href=url;
                        }else{
                            this.router.navigate(['/home']);
                        }
                    }else{
                        
                    }
                }


            },
            (err)=>{
                this.loading=false;
                console.log(err)
                AlertNotif.finish("Connexion","Echec de connexion","error")}
        )

    }

    refreshTokenAndRedirect(){
        var data={
            'grant_type' : 'refresh_token',
            'refresh_token' : this.local_service.getItem(globalName.refresh_token),
            'scope' : '',
        }
        if(this.visitor){
            data['client_id']=this.client_id;
            data['grant_type']=clientData.grant_type;
            data['client_secret']=this.client_secret;
        }else{
            data['client_id']=clientData.client_id;
            data['grant_type']=clientData.grant_type;
            data['client_secret']=clientData.client_secret;
        }
        data['ip']=this.ipAddress;
        this.user_auth_service.login(data).subscribe(
            (res:any)=>{
                this.loading=false;

                if(res.user.active){
                    this.local_service.setItem(globalName.token,res.access_token.accessToken)
                    this.local_service.setItem(globalName.current_user,res.user)
                    this.local_service.setItem(globalName.refresh_token,res.refresh_token)
                    const url=res.redirect_url+'?access_token='+res.accessToken+'&email='+res.user.email;
                    if(this.visitor){
                        window.location.href=url;
                    }else{
                        this.router.navigate(['/home']);
                    }
                }else{
                    this.needMailCheck=true;
                }

            },
            (err)=>{
                this.loading=false;
                console.log(err)
                AlertNotif.finish("Connexion","Echec de connexion","error")}
        )
    }


    resendMailCheclCode(){
      this.loading=true
        this.user_auth_service.resend({email:this.email}).subscribe(
            (res:any)=>{
                this.loading=false;
                localStorage.setItem("is_registered","");
                this.router.navigate(['/mail-check-code-resent']);
            },
            (err)=>{
                this.loading=false;
                console.log(err)
                AlertNotif.finish("Connexion","Echec de connexion","error")}
        )
    }



}
