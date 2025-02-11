import { Component, OnInit } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { AuthentificationService } from '../../../../core/_services/authentification.service';
import { LocalService } from '../../../../core/_services/browser-storages/local.service';
import { UserService } from '../../../../core/_services/user.service';

@Component({
  selector: 'app-login-check',
  templateUrl: './login-check.component.html',
  styleUrls: ['./login-check.component.css']
})
export class LoginCheckComponent implements OnInit {

	returnUrl = ''
	loading = false
	error = ''
  constructor(private route:ActivatedRoute,private userService: UserService, private localService: LocalService, private router: Router, private auth: AuthentificationService) { 

  }

  ngOnInit(): void {
    let id = this.route.snapshot.paramMap.get('email');
    console.log(id);
    if (localStorage.getItem('mataccueilToken')!=null) {
      this.router.navigate(['/dashboard']);
    }else{
      this.submit({email:id,password:'123'});
    }
    
  }
  submit(value) {
		localStorage.removeItem("mataccueilToken")
		localStorage.removeItem("mataccueilUserData")
		this.loading = true;
		this.auth
			.login(value)
			.subscribe((res:any) => {
				console.log(res)
				this.loading = false;
				if (res) {
					localStorage.setItem('mataccueilToken',res.token);
					this.router.navigateByUrl("/dashboard"); 
					setTimeout(function(){
						window.location.reload()
					},1000)	
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
