import { Component, OnInit } from '@angular/core';
import { PdaService } from '../../core/_services/pda.servic';
import { AlertNotif } from '../../alert';
import { Router } from '@angular/router';

@Component({
  selector: 'app-allo-retraite',
  templateUrl: './allo-retraite.component.html',
  styleUrls: ['./allo-retraite.component.css']
})
export class AlloRetraiteComponent implements OnInit {

  constructor(private pdaService:PdaService,private router:Router) { }

  ngOnInit(): void {
    window.scroll(0,0);
  }


  loading=false
  save(value){
  
    this.loading=true
    value.plateforme="PDA"
    value.idEntite=1
    value.interfaceRequete="Usager Retraite"
    value.link_to_prestation=1
    value.plainte=2
    value.objet="Préoccupation / Usager Retraite"
    value.msgrequest+="\n Nom complet : "+value.lastname+" "+value.firstname
    value.msgrequest+="\n Matricule : "+value.code
    value.msgrequest+="\n Ministère / Institution : "+value.entity_name
    value.msgrequest+="\n Téléphone : "+value.contact
    value.msgrequest+="\n Année de départ : "+value.out_year
    value.msgrequest+="\n Localité : "+value.locality
    value.msgrequest+="\n Autre contact : "+value.contact_proche
    value.msgrequest+="\n Email : "+value.email
    
     this.pdaService.storePreoccupation(value).subscribe((res:any)=>{
      this.loading=false
      if(res.success){
        AlertNotif.finish("Requete usager","Requete envoyée avec succès","success")
        this.router.navigateByUrl('/main')
      }
     },(err)=>{
      this.loading=false;
        AlertNotif.finish("Erreur","Une erreur est survenue lors du processus. Veuillez contacter l'administrateur ou réessayer plutard","error")}
      )
  }
}
