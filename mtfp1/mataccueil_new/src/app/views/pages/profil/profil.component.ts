import { Component, OnInit,Input } from '@angular/core';
import { LocalService } from '../../../core/_services/browser-storages/local.service';
import { Router } from '@angular/router';
import { Roles } from '../../../core/_models/roles';
import { UserService } from '../../../core/_services/user.service';
import { User } from '../../../core/_models/user.model';
import { AlertNotif } from '../../../alert';

@Component({
  selector: 'kt-profil',
  templateUrl: './profil.component.html',
  styleUrls: ['./profil.component.scss']
})
export class ProfilComponent implements OnInit {

  @Input() cssClasses = '';
  error=""
  errormessage=""
  current_role=""
  user:any
  file:File
  
  constructor(private localService:LocalService,private userService:UserService,private router:Router,private localStorageService:LocalService) { }

  ngOnInit(): void {
      this.current_role=localStorage.getItem('mataccueilUserRole')
      this.user=new User(this.localStorageService.getJsonValue("mataccueilUserData")) 
      console.log('-----------------------------------12')     
      console.log(this.user)     
  }
  
  saveUpdateProfil(value){
    if(value.newpassword==value.newpasswordconfirm){
      var params = {
        IdUtilisateur:this.user.id,
        newemail:value.newemail,
        newpassword:value.newpassword,
        newcontacts:value.newcontacts,
    }
      this.userService.updateProfil(params).subscribe((res:any)=>{
        this.localService.setJsonValue("mataccueilUserData",res);
        AlertNotif.finish('Modification profil', 'Votre mise à jour de profil été prise en compte avec succès. A présent nous vous déconnecterons et vous reconnecterez avec votre nouveau mot de passe.', 'success')
        this.signout()
      }, (err)=>{
        AlertNotif.finish('Modification profil', 'Une erreur est survenue, verifier votre connexion internet puis reessayer', 'error')
      }) 
    }
  }
  signout(){
    localStorage.removeItem('mataccueilToken')
    localStorage.removeItem('mataccueilUserRole')
    this.localStorageService.clearToken()
    this.router.navigateByUrl('/login')
  }
 
  updateUser(value){

    let formData=new FormData()
    formData.append("username",value.last_name+" "+value.first_name )
    formData.append("last_name",value.last_name)
    formData.append("phone",value.phone)
    formData.append("email",value.email)
    formData.append("first_name",value.first_name)
   
    if(this.file!=null){
      formData.append("profil_image",this.file)
    }
    this.userService.update(value,this.user.id).subscribe((res:any)=>{
      this.localService.setJsonValue("mataccueilUserData",res);
      AlertNotif.finish('Modification de profil', 'Votre mise à jour de profil été prise en compte avec succès', 'success')

      this.ngOnInit()
    }, (err)=>{
      AlertNotif.finish('Modification de profil', 'Une erreur est survenue, verifier votre connexion internet puis reessayer', 'error')
    }) 
  }
  onFileChange(event) {
    this.file=null
    if (event.target.files.length > 0) {
      this.file = event.target.files[0];
    //  this.form.get('avatar').setValue(file);
    }
  }

}
