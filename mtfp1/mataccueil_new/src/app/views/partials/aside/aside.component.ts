import { Component, OnInit, Input } from '@angular/core';
import { Roles } from '../../../core/_models/roles';
import { Router } from '@angular/router';
import { LocalService } from '../../../core/_services/browser-storages/local.service';
import { Subscription } from 'rxjs';
import { AuthentificationService } from '../../../core/_services/authentification.service';
import { ProfilService } from '../../../core/_services/profil.service';

declare var $: any;

@Component({
  selector: 'app-aside',
  templateUrl: './aside.component.html',
  styleUrls: ['./aside.component.css']
})
export class AsideComponent implements OnInit {

  admin_r=Roles.Admin
  sub_admin_r=Roles.SubAdmin
  agence_location_r=Roles.AgenceLocationVoiture
  restaurant_r=Roles.Restaurant
  hotel_r=Roles.Hotel
  tour_operator_r=Roles.TourOperateur
  current_user_role=''
  constructor(private router:Router,private localStorageService:LocalService,private authService:AuthentificationService,private profilService:ProfilService) { }

  @Input()
  get_user: any;

  user:any

  ngOnInit(): void {
    this.current_user_role=localStorage.getItem('mataccueilUserRole')
    
    if(this.get_user!=null && this.get_user!=undefined){
      this.user=this.get_user
    }

    if (localStorage.getItem('mataccueilUserData') != null) {
      this.user = this.localStorageService.getJsonValue("mataccueilUserData")
      // console.log(this.user)
    }
 
    // $.getScript('assets/js/jquery.min.js')
    // $.getScript('assets/js/bootstrap.bundle.min.js')
    // $.getScript('assets/js/metismenu.min.js')
    // $.getScript('assets/js/jquery.slimscroll.js')
    // $.getScript('assets/js/waves.min.js')
    // $.getScript('assets/js/app.js') 
    $.getScript('assets/js/compiled.min.js')
    // $.getScript('assets/js/testside.js')
  }

  apercuGuide(){
    this.profilService.get(this.user.idprofil).subscribe((res:any)=>{
      // console.log('------------------------------------')
      // console.log(res.fichier_guide)
      if(res.fichier_guide == null || res.fichier_guide == ''){
        alert("Aucune documentation n'est associée à ce profil")
      }else{
        var url= 'https://api.mataccueil.gouv.bj/rapports/'+res.fichier_guide
        window.open(url, "_blank")  
      }
    })
  }


}
