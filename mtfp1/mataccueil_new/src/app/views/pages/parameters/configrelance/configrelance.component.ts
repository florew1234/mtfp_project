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
import { TypeService } from '../../../../core/_services/type.service';
import { Type } from '../../../../core/_models/type.model';
import { LocalService } from '../../../../core/_services/browser-storages/local.service';
import { InstitutionService } from '../../../../core/_services/institution.service';



@Component({
  selector: 'app-configrelance',
  templateUrl: './configrelance.component.html',
  styleUrls: ['./configrelance.component.css']
})
export class ConfigrelanceComponent implements OnInit {

 
  @Input() cssClasses = '';
  page = 1;
  pageSize = 10;
  searchText=""
  closeResult = '';
  permissions:any[]
  error=""
  selected_iduse=""
  selected_idEntite=""
  selected_Entite=""
  ministere = []

  data: any[]=[];
  _temp: any[]=[];
  listuser = []

  selected = [
  ];
  current_permissions:any[]=[]
  collectionSize = 0;
  selected_data={ordre_relance:"",msg_relance:"",apartir_de:"",id_user:null,idEntite:null,id:null}

  search(){
    this.data=this._temp.filter(r => {
      const term = this.searchText.toLowerCase();
      return r.libelle.toLowerCase().includes(term) 
    })
    this.collectionSize=this.data.length
  }
  
  openAddModal(content) {
    
    // alert(this.selected_Entite)

    if(this.selected_Entite == '-1' || this.selected_Entite == null || this.selected_Entite == '' ){
      AlertNotif.finish("Erreur", "Veuillez selectionnez une institutions", 'error');
      return;
    }
    this.modalService.open(content, {ariaLabelledBy: 'modal-basic-title', size: "lg"}).result.then((result) => {
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
    this.modalService.open(content, {ariaLabelledBy: 'modal-basic-title', size: "lg"}).result.then((result) => {
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
    private institutionService:InstitutionService,
    private translate:TranslateService,
    private spinner: NgxSpinnerService,
    private localStorageService:LocalService,
    private activatedRoute: ActivatedRoute,
    ) {}


    user:any
    ngOnInit() {
      if (localStorage.getItem('mataccueilUserData') != null) {
        this.user = this.localStorageService.getJsonValue("mataccueilUserData")
      }
      this.listuser = []
      // this.selected_idEntite
      this.institutionService.getLisUsersParEntite(1).subscribe((res:any)=>{
        this.listuser = res
      })
      this.ministere = []
      this.institutionService.getAllEntite().subscribe((res: any) => {
        this.ministere = res
      })
     this.init()
    }
    user_prin = false
    checked(event, el) {
      this.selected_data = el
      if(this.selected_data.id_user == '-1'){
        this.user_prin = true
      }else{
        this.user_prin = false
      }
    }
    
  init(){
    this._temp=[]
    this.data=[]
    this.institutionService.getAll_Relance(this.selected_Entite).subscribe((res:any)=>{
      this.spinner.hide();
      this.data=res
      this._temp=this.data
      this.collectionSize=this.data.length
    })
  }
  
  create(value){

    if(this.selected_Entite == '-1' || this.selected_Entite == null || this.selected_Entite == '' ){
      AlertNotif.finish("Erreur", "Veuillez selectionnez une institutions", 'error');
      return;
    }
    value.idEntite = this.selected_Entite

    this.institutionService.createRelance(value).subscribe((res:any)=>{
     this.modalService.dismissAll()
     AlertNotif.finish("Nouvel ajout","Ajout effectué avec succès" , 'success')
     this.user_prin = false

      // this.init() 
    },(err)=>{
      
      if(err.error.detail!=null){    
        AlertNotif.finish("Nouvel ajout", err.error.detail, 'error')
      }else{
        AlertNotif.finish("Nouvel ajout", "Erreur, Verifiez que vous avez une bonne connexion internet", 'error')
      }
    })
  }

  archive(){
    AlertNotif.finishConfirm("Suppression",
    "Cette action est irreversible. Voulez-vous continuer ?").then((result) => {
      if (result.value) {
      this.institutionService.deleteRelance(this.selected_data.id).subscribe((res:any)=>{
        this.init()
        AlertNotif.finish("Suppression", "Suppression effectuée avec succès", 'success')
        this.init()
      }, (err)=>{
        AlertNotif.finish("Suppression", "Erreur, Verifiez que vous avez une bonne connexion internet", 'error')
      })
    }
   })
  }
  edit(value) {

    if(this.selected_Entite == '-1' || this.selected_Entite == null || this.selected_Entite == '' ){
      AlertNotif.finish("Erreur", "Veuillez selectionnez une institutions", 'error');
      return;
    }
    value.idEntite = this.selected_Entite

    value.id=this.selected_data.id
    this.institutionService.updateRelance(value,this.selected_data.id).subscribe((res)=>{
      this.modalService.dismissAll()
      this.init()
      AlertNotif.finish("Nouvelle modification",  "Motification effectué avec succès", 'success')
    }, (err)=>{
      AlertNotif.finish("Nouvelle modification", "Erreur, Verifiez que vous avez une bonne connexion internet", 'error')
    })
	}


}
