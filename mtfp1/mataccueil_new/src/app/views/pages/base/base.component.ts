import { Component, OnInit } from '@angular/core';
import { UserService } from '../../../core/_services/user.service';
import { LocalService } from '../../../core/_services/browser-storages/local.service';
import { TranslateService } from '@ngx-translate/core';
import { Roles } from '../../../core/_models/roles';
import { AuthentificationService } from '../../../core/_services/authentification.service';


@Component({
  selector: 'app-base',
  templateUrl: './base.component.html',
  styleUrls: ['./base.component.css']
})
export class BaseComponent implements OnInit {

  user: any
  constructor(private userService: UserService, private authService: AuthentificationService, private localService: LocalService, private translateService: TranslateService) { }

  ngOnInit(): void {
    if (localStorage.getItem('mataccueilUserData') != null) {
      this.user = this.localService.getJsonValue('mataccueilUserData')
    }else{
      this.authService.getUserByToken().subscribe((res: any) => {
        console.log(res)
       this.user=res
       this.localService.setJsonValue('mataccueilUserData', res)
      })

    }
  }

}
