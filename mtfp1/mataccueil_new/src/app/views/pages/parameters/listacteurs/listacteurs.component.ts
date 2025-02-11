import { Component, OnInit, Input } from '@angular/core';
import { PipeTransform } from '@angular/core';
import { DecimalPipe } from '@angular/common';
import { FormControl } from '@angular/forms';

import { Observable } from 'rxjs';
import { map, startWith } from 'rxjs/operators';
import {NgbModal, ModalDismissReasons} from '@ng-bootstrap/ng-bootstrap';
import { Router, ActivatedRoute } from '@angular/router';
import { UserService } from '../../../../core/_services/user.service';

import { NgxSpinnerService } from 'ngx-spinner';
import { AlertNotif } from '../../../../alert';
import { TranslateService } from '@ngx-translate/core';
import { User } from '../../../../core/_models/user.model';
import { Roles } from '../../../../core/_models/roles';
import { StructureService } from '../../../../core/_services/structure.service';
import { ActeurService } from '../../../../core/_services/acteur.service';
import { Acteur } from '../../../../core/_models/acteur.model';
import { LocalService } from '../../../../core/_services/browser-storages/local.service';

@Component({
  selector: 'app-listacteurs',
  templateUrl: './listacteurs.component.html',
  styleUrls: ['./listacteurs.component.css']
})
export class ListacteursComponent implements OnInit {

  @Input() cssClasses = '';
  page = 1;
  pageSize = 10;
  searchText=""
  commune = []
  selectedDepart = null
  closeResult = '';
  permissions:any[]
  error=""
  data: any[]=[];
  _temp: any[]=[];

  selected = [
  ];
  current_permissions:any[]=[]
  collectionSize = 0;
  selected_data:Acteur

  search(){ 
    this.data=this._temp.filter(r => {
      const term = this.searchText.toLowerCase();
      return r.nomprenoms.toLowerCase().includes(term) ||
      (r.structure==null ? '' : r.structure.libelle).toString().toLowerCase().includes(term)
    })
    this.collectionSize=this.data.length
  }
  
  onDepartChange(event){
    console.log(event.target.value)
    this.selectedDepart=+event.target.value
    this.chargerCommune(this.selectedDepart)
  }
  chargerCommune(idDepartt){
    this.commune = []
    this.acteursService.getAllCommune(idDepartt).subscribe((res: any) => {
      this.commune = res
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
    private userService: UserService,
    private router:Router,
    private structureService:StructureService,
    private acteursService:ActeurService,
    private translate:TranslateService,
    private spinner: NgxSpinnerService,
    private activatedRoute: ActivatedRoute,
    private localStorageService:LocalService
    ) {}

    structures:[]=[]
    departement:[]=[]

    user:any
    idDepa:any
    ngOnInit() {
      if (localStorage.getItem('mataccueilUserData') != null) {
        this.user = this.localStorageService.getJsonValue("mataccueilUserData")
      }
     this.init()
    }
    checked(event, el) {
      this.selected_data = el
      console.log(el)
      this.idDepa = el.commune.depart_id
      this.chargerCommune(this.idDepa)
    }
    
  init(){
    this._temp=[]
    this.data=[]
    this.acteursService.getAll(this.user.idEntite).subscribe((res:any)=>{
      this.spinner.hide();
      this.data=res
      this._temp=this.data
      this.collectionSize=this.data.length
    })
    
    this.structures=[]
    this.structureService.getAll(0,this.user.idEntite).subscribe((res:any)=>{
      this.spinner.hide();
      this.structures=res
    })
    this.departement=[]
    this.acteursService.getAllDepart().subscribe((res:any)=>{
      this.departement=res
    })
  }
  
  create(value){
    value.idEntite=this.user.idEntite

    if(value.idDepart == null || value.idDepart == ""){
      AlertNotif.finish("Erreur","Veuillez choisir un département.", 'error')
    }else if(value.idComm == null || value.idComm == ""){
      AlertNotif.finish("Erreur","Veuillez choisir une commune.", 'error')
    }else{
      this.acteursService.createGra(value).subscribe((res:any)=>{
        
       this.modalService.dismissAll()
       //this.translate.instant('HOME.TITLE')
       AlertNotif.finish("Nouvel ajout","Ajout effectué avec succès" , 'success')
        this.init() 
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
      this.acteursService.delete(this.selected_data.id).subscribe((res:any)=>{
        this.init()
        AlertNotif.finish("Suppression", "Suppression effectuée avec succès", 'success')
        
      }, (err)=>{
        AlertNotif.finish("Suppression", "Erreur, Verifiez que vous avez une bonne connexion internet", 'error')
      })
    }
   })
  }
  edit(value) {
    value.id=this.selected_data.id
    value.idEntite=this.user.idEntite
    
    if(value.idDepart == null || value.idDepart == ""){
      AlertNotif.finish("Erreur","Veuillez choisir un département.", 'error')
    }else if(value.idComm == null || value.idComm == ""){
      AlertNotif.finish("Erreur","Veuillez choisir une commune.", 'error')
    }else{
      this.acteursService.update(value,this.selected_data.id).subscribe((res)=>{
        this.modalService.dismissAll()
        this.init()
        AlertNotif.finish("Nouvelle modification",  "Motification effectué avec succès", 'success')
      }, (err)=>{
        AlertNotif.finish("Nouvelle modification", "Erreur, Verifiez que vous avez une bonne connexion internet", 'error')
      })
    }
	}


}
