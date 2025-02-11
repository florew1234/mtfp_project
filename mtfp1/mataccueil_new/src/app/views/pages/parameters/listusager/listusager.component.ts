import { Component, OnInit, Input } from '@angular/core';
import { PipeTransform } from '@angular/core';
import { DecimalPipe } from '@angular/common';
import { FormControl } from '@angular/forms';

import { Observable, Subject } from 'rxjs';
import { map, startWith } from 'rxjs/operators';
import {NgbModal, ModalDismissReasons} from '@ng-bootstrap/ng-bootstrap';
import { Router, ActivatedRoute } from '@angular/router';
import { UserService } from '../../../../core/_services/user.service';

import { NgxSpinnerService } from 'ngx-spinner';
import { AlertNotif } from '../../../../alert';
import { TranslateService } from '@ngx-translate/core';
import { User } from '../../../../core/_models/user.model';
import { Roles } from '../../../../core/_models/roles';
import { ProfilService } from '../../../../core/_services/profil.service';
import { UsagerService } from '../../../../core/_services/usager.service';
import { Usager } from '../../../../core/_models/usager.model';
import { ServiceService } from '../../../../core/_services/service.service';
import { NatureRequeteService } from '../../../../core/_services/nature-requete.service';
import { TypeService } from '../../../../core/_services/type.service';
import { RequeteService } from '../../../../core/_services/requete.service';
import { LocalService } from '../../../../core/_services/browser-storages/local.service';
import { RdvService } from '../../../../core/_services/rdv.service';
import { RdvCreneauService } from '../../../../core/_services/rdv-creneau.service';
import { StructureService } from '../../../../core/_services/structure.service';

@Component({
  selector: 'app-listusager',
  templateUrl: './listusager.component.html',
  styleUrls: ['./listusager.component.css']
})
export class ListusagerComponent implements OnInit {

  @Input() cssClasses = '';
  page = 1;

  errormessage=""
  pageSize = 10;
  searchText=""
  closeResult = '';
  permissions:any[]
  error=""
  data: any[]=[];
  dataNT: any[] = [];
  _temp: any[]=[];

  selected = [
  ];
  current_permissions:any[]=[]
  collectionSize = 0;
  selected_data:Usager
  link_to_prestation=1
  selected_type_preoccupation=0
  structures=[]

  search(){ 
    this.data=[]
    this._temp=[]
    this.usagersService.getAll(this.searchText,this.page).subscribe((res:any)=>{
      this.spinner.hide();
      this.data=res.data
      this._temp=this.data
      this.subject.next(res);
    })
  }
  
  openAddModal(content) {
    this.modalService.open(content, {ariaLabelledBy: 'modal-basic-title'}).result.then((result) => {
      this.closeResult = `Closed with: ${result}`;
    }, (reason) => {
      this.closeResult = `Dismissed ${this.getDismissReason(reason)}`;
    });
  }

  openEditModal(content){
    if (this.selected_data == null) {
      AlertNotif.finish("Erreur", "Veuillez selectionnez un élément puis réessayer", 'error');
      return;
    }
    this.modalService.open(content, {ariaLabelledBy: 'modal-basic-title'}).result.then((result) => {
      this.closeResult = `Closed with: ${result}`;
    }, (reason) => {
      this.closeResult = `Dismissed ${this.getDismissReason(reason)}`;
    });
  }

  requetes=[]
  openRdvModal(content){
    if (this.selected_data == null) {
      AlertNotif.finish("Erreur", "Veuillez selectionnez un élément puis réessayer", 'error');
      return;
    }
    this.requeteService.getAllForUsager(
      this.selected_data.id
      , 1).subscribe((res: any) => {
          this.requetes = res
          this.modalService.open(content, {ariaLabelledBy: 'modal-basic-title'}).result.then((result) => {
            this.closeResult = `Closed with: ${result}`;
          }, (reason) => {
            this.closeResult = `Dismissed ${this.getDismissReason(reason)}`;
          });
      })
  }

  
  private getDismissReason(reason: any): string {
    if (reason === ModalDismissReasons.ESC) {
      return 'by pressing ESC';
    } else if (reason === ModalDismissReasons.BACKDROP_CLICK) {
      return 'by clicking on a backdrop';
    } else {
      return `with: ${reason}`;
    }
  }
  

  constructor(
    private modalService: NgbModal,
    private router:Router,
    private profilService:ProfilService,
    private usagersService:UsagerService,
    private prestationService:ServiceService,
    private translate:TranslateService,
    private natureService:NatureRequeteService,
    private structureService:StructureService,
    private spinner: NgxSpinnerService,
    private requeteService:RequeteService,
    private activatedRoute: ActivatedRoute,
    private rdvService:RdvService,
    private rdvCreneauService:RdvCreneauService,
    private thematiqueService:TypeService,
    private localStorageService:LocalService
    ) {}

    departements:[]=[]
    profils:[]=[]
  
  user:any
  ngOnInit() {

    if (localStorage.getItem('mataccueilUserData') != null) {
      this.user = this.localStorageService.getJsonValue("mataccueilUserData")
    }

    this.activatedRoute.queryParams.subscribe(x => this.init(x.page || 1));

    this.subject.subscribe((val) => {
     this.pager=val
     this.page=this.pager.current_page

     let pages=[]
     if(this.pager.last_page  <= 5){
      for (let index = 1; index <= this.pager.last_page; index++) {
        pages.push(index)
      }
     }else{
       let start=(this.page >3 ? this.page-2 : 1 )
       let end=(this.page+2 < this.pager.last_page ? this.page+2 : this.pager.last_page )
      for (let index = start; index <= end; index++) {
        pages.push(index)
      }
     }
    
     this.pager.pages=pages
  });
  }
    checked(event, el) {
    this.selected_data = el
    console.log(el)
    
    this.requeteService.getAllForUsagerNT(el.id,1).subscribe((res: any) => {
      this.dataNT = res
    })
    
  }
  

  natures=[]
  services=[]
  detailpiece=[]
  descrCarr=[]
  __services=[]
  themes=[]
    pager: any = {current_page: 0,
    data:[],
    last_page: 0,
    per_page: 0,
    to: 0,
    total: 0
  }
  subject = new Subject<any>();
  Null=null
  rdvcreneaus=[]

  init(page){

    this._temp=[]
    this.data=[]
    this.usagersService.getAll(null,page).subscribe((res:any)=>{
      this.spinner.hide();
      this.data=res.data
      this._temp=this.data
      this.subject.next(res);
    })
 
    this.departements=[]
    this.usagersService.getAllDepartement().subscribe((res:any)=>{
      this.departements=res
    })

 
    this.structures = []
    this.structureService.getAll(1,this.user.idEntite).subscribe((res:any)=>{
      this.structures = res
    })

    this.rdvcreneaus = []
    this.rdvCreneauService.getAll(this.user.idEntite).subscribe((res: any) => {
      this.rdvcreneaus = res
    })
   this.natures=[]
    this.natureService.getAll(this.user.idEntite).subscribe((res:any)=>{
      this.natures=res
    })

    this.themes=[]
    this.thematiqueService.getAll(this.user.idEntite).subscribe((res:any)=>{
      this.themes=res
    })

  }
  
  create(value){
    if(value.password!=value.conf_password && value.password != ""){
      this.error="Les deux mot de passe doivent être identique"
    }else{
      this.usagersService.create(value).subscribe((res:any)=>{
      
        this.modalService.dismissAll()
        //this.translate.instant('HOME.TITLE')
        AlertNotif.finish("Nouvel ajout","Ajout effectué avec succès" , 'success')
         this.init(this.page) 
       },(err)=>{
         
         if(err.error.detail!=null){    
           AlertNotif.finish("Nouvel ajout", err.error.detail, 'error')
         }else{
           AlertNotif.finish("Nouvel ajout", "Erreur, Verifiez que vous avez une bonne connexion internet", 'error')
         }
       })
    }
    
  }


  archive(){
    if (this.selected_data == null) {
      AlertNotif.finish("Erreur", "Veuillez selectionnez un élément puis réessayer", 'error');
      return;
    }
    AlertNotif.finishConfirm("Suppression",
    "Cette action est irreversible. Voulez-vous continuer ?").then((result) => {
      if (result.value) {
      this.usagersService.delete(this.selected_data.id).subscribe((res:any)=>{
       this.init(this.page)
        AlertNotif.finish("Suppression", "Suppression effectuée avec succès", 'success')
     
      }, (err)=>{
        AlertNotif.finish("Suppression", "Erreur, Verifiez que vous avez une bonne connexion internet", 'error')
      })
    }
   })
  }

  edit(value) {
    value.id=this.selected_data.id
    value.code=this.selected_data.code
    value.codeComplet=this.selected_data.codeComplet
    if(value.password!=value.conf_password){
      value.password=""
      this.error="Le  mot de passe n'a pas été pris en compte car les deux ne sont pas identique"
    }else{
      this.usagersService.update(value,this.selected_data.id).subscribe((res)=>{
        this.modalService.dismissAll()
        this.init(this.page)
        AlertNotif.finish("Nouvelle modification",  "Motification effectué avec succès", 'success')
      }, (err)=>{
        AlertNotif.finish("Nouvelle modification", "Erreur, Verifiez que vous avez une bonne connexion internet", 'error')
      })
    }
    
	}

  chargerPrestation(event){
    this.services=[]
    this.prestationService.getAllType(event.target.value).subscribe((res:any)=>{
      this.services=res
    })
    
    this.thematiqueService.get(event.target.value).subscribe((res:any)=>{
      this.descrCarr = res.descr
    })
    
  }

  openDetailModal(event,content){

    this.detailpiece=[]
    console.log(event.target.value)
    this.prestationService.getServPiece(event.target.value).subscribe((res:any)=>{
      this.detailpiece=res
    })
    
    this.modalService.open(content, {ariaLabelledBy: 'modal-basic-title'}).result.then((result) => {
      this.closeResult = `Closed with: ${result}`;
    }, (reason) => {
      this.closeResult = `Dismissed ${this.getDismissReason(reason)}`;
    });
  }


  addRequeteusager(value){
    let service = null
    if (this.link_to_prestation==1 || this.selected_type_preoccupation==0) {
      service = this.services.filter(e => (e.id == value.idPrestation))[0]
    }else{
      service=this.services.filter(e => (e.hide_for_public == 1))[0]
    }
    
    if(!value.objet){
      AlertNotif.finish("Renseigner l'objet", "Champ obligatoire", 'error')
    }else if(!value.msgrequest){
      AlertNotif.finish("Renseigner le message", "Champ obligatoire", 'error')
    }else{
      var param = {
        objet: value.objet,
        idPrestation: this.link_to_prestation==0  ? service.id : value.idPrestation,
        nbreJours: service == null ? 0 : service.nbreJours,
        msgrequest: value.msgrequest,
        email:this.selected_data.email,
        nom:this.selected_data.nom,
        tel:this.selected_data.tel,
        idDepartement:this.selected_data.idDepartement,
        interfaceRequete: this.link_to_prestation==1 ? "USAGER"  : "SRU" ,
        idUser:this.user.id,
        plainte: value.plainte,
        visible:1
     };
     this.requeteService.create(param).subscribe((rest:any)=>{
      this.modalService.dismissAll()
      AlertNotif.finish("Ajout requête",  "Requête ajoutée avec succès", 'success')
    })     

    }
  }

  statut=1

  saveRdv(value) {
    var param = {
      idUsager: this.user.id,
      objet: this.selected_el_obj,
      idRdvCreneau: value.idRdvCreneau,
      codeRequete: value.codeRequete,
      dateRdv: value.dateRdv,
      idEntite:this.user.idEntite,
      idStructure:value.idStructure,
      statut: this.statut,
      attente: value.attente,
    }
    this.rdvService.create(param).subscribe((res: any) => {
      this.modalService.dismissAll()
      AlertNotif.finish("Prise de rdv", "RDV envoyé avec succès", 'succes');
    })
  }

  selected_el_obj = ""


  show_structures=false
  selectRequest(event) {
    if(event.target.value!="0"){
      this.show_structures=false
      this.selected_el_obj = this.data.find(e => (e.codeRequete == event.target.value)).objet
    }else{
      this.show_structures=true
      this.selected_el_obj = ""
    }
  }
}
