import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { LocalService } from '../../../core/_services/browser-storages/local.service';
import { Roles } from '../../../core/_models/roles';
import {TranslateService} from '@ngx-translate/core';
import { UserService } from '../../../core/_services/user.service';


@Component({
  selector: 'app-user-panel',
  templateUrl: './user-panel.component.html',
  styleUrls: ['./user-panel.component.css']
})
export class UserPanelComponent implements OnInit {

  constructor(private userService:UserService,private router:Router,private localStorageService:LocalService,private translateService: TranslateService) { }
  
  current_role=""
  user:any
 
  ngOnInit(): void {
    this.current_role=localStorage.getItem('mataccueilUserRole')
      this.user=this.localStorageService.getJsonValue("mataccueilUserData")
      if(this.current_role!=Roles.Admin && this.current_role!=Roles.SubAdmin){
      }
     
    
        this.translateService.use(this.user.default_language);
  }


  

  signout(){
    localStorage.removeItem('mataccueilToken')
    localStorage.removeItem('mataccueilUserRole')
    this.localStorageService.clearToken()
    this.router.navigateByUrl('/login')
  }

}
