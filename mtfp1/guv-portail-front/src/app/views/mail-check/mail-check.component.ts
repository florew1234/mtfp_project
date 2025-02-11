import { Component, OnInit } from '@angular/core';
import {ActivatedRoute, Router} from '@angular/router';
import {AlertNotif} from '../../alert';
import {AuthService} from '../../core/_services/auth.service';

@Component({
  selector: 'app-mail-check',
  templateUrl: './mail-check.component.html',
  styleUrls: ['./mail-check.component.css']
})
export class MailCheckComponent implements OnInit {
token:string;

  constructor(private route:ActivatedRoute,private router:Router,private user_auth_service:AuthService) {
    if(this.route.snapshot.paramMap.get("token")==null){
      this.router.navigate(['/main'])
    }
  }

  ngOnInit(): void {
    this.token=this.route.snapshot.paramMap.get("token")
  }

    checkMail(){

        this.user_auth_service.checkMail({token:this.token}).subscribe(
            (res:any)=>{

                this.router.navigate(['/main']);
                AlertNotif.finish("Mail","Mail confirmé","success")
            },
            (err)=>{
                this.router.navigate(['/main']);
                AlertNotif.finish("Mail","Mail non confirmé","success")}
        )

    }
}
