import { Component, OnInit } from '@angular/core';
import { AlertNotif } from 'src/app/alert';
import { SettingService } from 'src/app/core/_services/setting.service';

@Component({
  selector: 'app-settings',
  templateUrl: './settings.component.html',
  styleUrls: ['./settings.component.css']
})
export class SettingsComponent implements OnInit {
setting:any={
  header_text:""
}
settingRecup:any
  constructor(  
  private settingService:SettingService
  ) { }

  ngOnInit(): void {
   this.settingRecup= localStorage.getItem('mataccueilSettings');
   if(this.settingRecup != "null"){
    console.log(this.settingRecup)

    this.setting=JSON.parse(this.settingRecup)
   } 
   console.log(this.setting)
  }

  set(value){


    if(this.settingRecup==undefined || this.settingRecup=="null"){
      this.settingService.create(value).subscribe((res:any)=>{
        localStorage.setItem('mataccueilSettings',JSON.stringify(res.data))
        AlertNotif.finish("Paramètre général","Paramètre crée avec succès")

      })
    }else{
      this.settingService.update( this.setting.id,value).subscribe((res:any)=>{
        localStorage.setItem('mataccueilSettings',JSON.stringify(res.data))
          AlertNotif.finish("Paramètre général","Paramètre mis à jour avec succès")
      })
    }

  }

}
