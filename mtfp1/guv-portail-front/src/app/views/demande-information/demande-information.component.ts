import { Component, OnInit, ViewChild } from '@angular/core';
import { AlertNotif } from '../../alert';
import { Router } from '@angular/router';
import { PdaService } from '../../core/_services/pda.servic';
import {NgbModal, ModalDismissReasons} from '@ng-bootstrap/ng-bootstrap';



@Component({
  selector: 'app-demande-information',
  templateUrl: './demande-information.component.html',
  styleUrls: ['./demande-information.component.css']
})
export class DemandeInformationComponent implements OnInit {

  active=1
  constructor(private router:Router, private pdaService:PdaService,private modalService:NgbModal) { }
  prestations=[]
  _temp=[]

  recup_data:any
  closeResult=""
  @ViewChild('contentRetraite') contentRetraite : any;
  @ViewChild('contentCarriere') contentCarriere : any;
  @ViewChild('contentFormation') contentFormation : any;

  open(content) {
    this.modalService.open(content, {ariaLabelledBy: 'modal-basic-title'}).result.then((result) => {
      this.closeResult = `Closed with: ${result}`;
    }, (reason) => {
      this.closeResult = `Dismissed ${this.getDismissReason(reason)}`;
    });
  }

  private getDismissReason(reason: any): string {
    this.recup_data=null
    if (reason === ModalDismissReasons.ESC) {
      return 'by pressing ESC';
    } else if (reason === ModalDismissReasons.BACKDROP_CLICK) {
      return 'by clicking on a backdrop';
    } else {
      return `with: ${reason}`;
    }
  }

  structures=[];

  ngOnInit(): void {
    window.scroll(0,0);

    this.pdaService.getPrestations().subscribe(
      (res:any)=>{
          this._temp=res;
          this.prestations=this._temp.filter(e=>(e.idParent==15 || e.idParent==16 || e.idParent==106))

      })
  }
  showPrestation(id){
    return this.prestations.filter(e=>(e.id==id))[0].libelle
  }

  loading=false
  loading2=false
  loading3=false

  savePension(value){
    this.recup_data=value
    this.modalService.open(this.contentRetraite);
  }
  saveCarriere(value){
    this.recup_data=value
    this.modalService.open(this.contentCarriere);
  }
  saveFormation(value){
    this.recup_data=value
    this.modalService.open(this.contentFormation);
  }

  validatePension(){
    let value=this.recup_data
    value.plateforme="GUV"
    value.idEntite=1
    value.interfaceRequete="Information Retraite"
    value.link_to_prestation=1
    value.idPrestation=2496; 
    value.msgrequest+="\n Nom complet : "+value.lastname+" "+value.firstname
    value.msgrequest+="\n Matricule : "+value.code
    value.msgrequest+="\n Ministère / Institution : "+value.entity_name
    value.msgrequest+="\n Téléphone : "+value.contact
    value.msgrequest+="\n Année de départ : "+value.out_year
    value.msgrequest+="\n Localité : "+value.locality
    value.msgrequest+="\n Autre contact : "+value.contact_proche
    value.msgrequest+="\n Email : "+value.email
    
    this.loading=true
     this.pdaService.storePreoccupation(value).subscribe((res:any)=>{
      this.loading=false
      this.modalService.dismissAll()
      if(res.success){
        AlertNotif.finish("Demande d'information pension","Demande envoyée avec succès","success")
        this.router.navigateByUrl('/main')
      }
     }, (err)=>{
      this.loading=false;
        AlertNotif.finish("Erreur","Une erreur est survenue lors du processus. Veuillez contacter l'administrateur ou réessayer plutard","error")}
      )
  }
  validateCarriere(){
    let value=this.recup_data
    value.plateforme="GUV"
    value.idEntite=1
    value.interfaceRequete="Information Carrirere"
    value.idPrestation=2497;
    value.plainte=2
    value.link_to_prestation=1
    value.msgrequest+="\n Nom complet : "+value.lastname+" "+value.firstname
    value.msgrequest+="\n Matricule : "+value.code
    value.msgrequest+="\n Ministère / Institution : "+value.entity_name
    value.msgrequest+="\n Téléphone : "+value.contact
    value.msgrequest+="\n Grade actuel : "+value.grade_actuel
    value.msgrequest+="\n Grade attendu : "+value.grade_attendu
    value.msgrequest+="\n Email : "+value.email
    this.loading2=true
    this.pdaService.storePreoccupation(value).subscribe((res:any)=>{
      this.loading2=false
      this.modalService.dismissAll()
        if(res.success){
          AlertNotif.finish("Demande d'information carrère","Demande envoyée avec succès","success")
          this.router.navigateByUrl('/main')
        }
     }, (err)=>{
      this.loading2=false;
        AlertNotif.finish("Erreur","Une erreur est survenue lors du processus. Veuillez contacter l'administrateur ou réessayer plutard","error")}
      )
  }
  validateFormation(){
    let value=this.recup_data
    value.plateforme="GUV"
    value.idEntite=1
    value.plainte=2
    value.interfaceRequete="Information Formation"
    value.idPrestation=2498;
    value.link_to_prestation=1
    value.msgrequest+="\n Nom complet : "+value.lastname+" "+value.firstname
    value.msgrequest+="\n Téléphone : "+value.contact
    value.msgrequest+="\n Email : "+value.email
    this.loading3=true
    this.pdaService.storePreoccupation(value).subscribe((res:any)=>{
      this.loading3=false
      this.modalService.dismissAll()
        if(res.success){
          AlertNotif.finish("Demande d'information formation","Demande envoyée avec succès","success")
          this.router.navigateByUrl('/main')
        }
     }, (err)=>{
      this.loading3=false;
        AlertNotif.finish("Erreur","Une erreur est survenue lors du processus. Veuillez contacter l'administrateur ou réessayer plutard","error")}
      )
  }

}
