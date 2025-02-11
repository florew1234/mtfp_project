import { Component, OnInit } from '@angular/core';
import { RequeteService } from '../../../../core/_services/requete.service';
import { AlertNotif } from '../../../../alert';
import { ActivatedRoute } from '@angular/router';

@Component({
  selector: 'app-complement-information',
  templateUrl: './complement-information.component.html',
  styleUrls: ['./complement-information.component.css']
})
export class ComplementInformationComponent implements OnInit {

  constructor(private requeteService:RequeteService,private activatedRoute:ActivatedRoute) { }
  data:any={
    message:"",
    id:""
  }
  Null=null
  ngOnInit(): void {
    if (this.activatedRoute.snapshot.paramMap.get('id') !=null) {
      this.requeteService.getReponseRapide(this.activatedRoute.snapshot.paramMap.get('id')).subscribe((res: any) => {
        this.data=res.data
      })
    }
    
  }

  loading=false
  save(value) {
    var param = {
      complement: value.complement,
      id: this.data.id,
    };
    this.loading=true
    this.requeteService.complementReponse(param).subscribe((rest: any) => {
   
      this.loading=false
        this.ngOnInit()
        AlertNotif.finish("RENSEIGNEMENTS", "Renseignements envoyé avec succès ", 'success')
     
    })
  }


}
