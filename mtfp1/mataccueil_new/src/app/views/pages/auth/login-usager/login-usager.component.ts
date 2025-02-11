import { Component, OnInit } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { AuthentificationService } from '../../../../core/_services/authentification.service';
import { LocalService } from '../../../../core/_services/browser-storages/local.service';
import {TranslateService} from '@ngx-translate/core';
import { Roles } from '../../../../core/_models/roles';

@Component({
  selector: 'app-login-usager',
  templateUrl: './login-usager.component.html',
  styleUrls: ['./login-usager.component.css']
})
export class LoginUsagerComponent implements OnInit {

  returnUrl=''
  loading=false
  error=''

  constructor(private activatedRoute:ActivatedRoute,private translateService: TranslateService,private localStorageService:LocalService,private route:ActivatedRoute,private router:Router, private auth:AuthentificationService) { }
  lang="fr"
  ngOnInit(): void {

  }

  submit(value) {
		this.loading = true;
		this.auth
			.loginUsager(value)
			.subscribe((res:any) => {
				console.log(res)
				this.loading = false;
				if (res) {
          this.localStorageService.setJsonValue('mataccueilUserData',res);
          
					this.router.navigateByUrl('/usager/espace'); 
				}
				
			},err => {
				console.log(err)
				this.loading = false; 
				if(err.error.non_field_errors!=null){
					this.error=err.error.non_field_errors[0]
				}
				});
	}


}
