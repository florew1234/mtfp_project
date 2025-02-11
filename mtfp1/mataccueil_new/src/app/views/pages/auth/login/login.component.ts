import { Component, OnInit } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { AuthentificationService } from '../../../../core/_services/authentification.service';
import { LocalService } from '../../../../core/_services/browser-storages/local.service';
import {TranslateService} from '@ngx-translate/core';
import { Roles } from '../../../../core/_models/roles';
import { SettingService } from 'src/app/core/_services/setting.service';


@Component({
  selector: 'app-login',
  templateUrl: './login.component.html',
  styleUrls: ['./login.component.css']
})
export class LoginComponent implements OnInit {

  returnUrl=''
  loading=false
  error=''

  constructor(private activatedRoute:ActivatedRoute,private translateService: TranslateService,private localStorageService:LocalService,private route:ActivatedRoute,private router:Router, private auth:AuthentificationService, private settingService: SettingService ) { }
  lang="fr"
  ngOnInit(): void {

	this.lang=this.activatedRoute.snapshot.paramMap.get('lang');
	if(this.lang=="fr" || this.lang=="en"){
		this.translateService.use(this.lang);
	}else{
		this.router.navigateByUrl('/login');
	}
	if(this.lang==null){
		this.lang="fr"
		this.translateService.use("fr");
	  }
    this.route.queryParams.subscribe(params => {
			this.returnUrl = params.returnUrl || '/dashboard';
		});
	if (localStorage.getItem('mataccueilToken')!=null) {
		this.router.navigate(['/dashboard']);
	}	
  }

  submit(value) {
	  
		localStorage.removeItem("mataccueilToken")
		localStorage.removeItem("mataccueilUserData")
		this.loading = true;
		this.auth
			.login(value)
			.subscribe((res:any) => {
				this.loading = false;
				if (res) {
						this.settingService.get().subscribe((result:any)=>{
							localStorage.setItem('mataccueilSettings',JSON.stringify(result.data));
							localStorage.setItem('mataccueilToken',res.token);
							this.router.navigateByUrl("/dashboard"); 
							setTimeout(function(){
								window.location.reload()
							},1000)	
						})
				
				}
			},err => {
				console.log(err)
				this.loading = false; 
				if(err.error.error=="invalid_credentials"){
					this.error="Email ou mot de passe incorrect"
				}else{
					this.error="Erreur de connexion ou paramètres incorrects"
				}
			});
	}

}
