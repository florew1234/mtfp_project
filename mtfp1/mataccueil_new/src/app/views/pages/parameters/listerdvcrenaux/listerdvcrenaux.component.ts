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
import { RdvCreneauService } from '../../../../core/_services/rdv-creneau.service';
import { RdvCreneau } from '../../../../core/_models/rdv-creneau.model';
import { LocalService } from '../../../../core/_services/browser-storages/local.service';


@Component({
  selector: 'app-listerdvcrenaux',
  templateUrl: './listerdvcrenaux.component.html',
  styleUrls: ['./listerdvcrenaux.component.css']
})
export class ListerdvcrenauxComponent implements OnInit {

  @Input() cssClasses = '';
  page = 1;
  pageSize = 10;
  searchText=""
  closeResult = '';
  permissions:any[]
  error=""
  data: any[]=[];
  _temp: any[]=[];

  selected = [
  ];
  current_permissions:any[]=[]
  collectionSize = 0;
  selected_data:RdvCreneau

  search(){ 
    this.data=this._temp.filter(r => {
      const term = this.searchText.toLowerCase();
      return r.heureDebut.toLowerCase().includes(term) ||
      r.heureFin.toLowerCase().includes(term) 
    })
    this.collectionSize=this.data.length
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
    private rdvCreneauService:RdvCreneauService,
    private translate:TranslateService,
    private spinner: NgxSpinnerService,
    private activatedRoute: ActivatedRoute,
    private localStorageService:LocalService
    ) {}


  user:any
  ngOnInit() {
    if (localStorage.getItem('mataccueilUserData') != null) {
      this.user = this.localStorageService.getJsonValue("mataccueilUserData")

    }
    this.init()
  }
    checked(event, el) {
    this.selected_data = el
  }
  
  init(){
    this._temp=[]
    this.data=[]
    this.rdvCreneauService.getAll(this.user.idEntite).subscribe((res:any)=>{
      this.spinner.hide();
      this.data=res
      this._temp=this.data
      this.collectionSize=this.data.length
    })
  }
  
  create(value){
    value.idEntite=this.user.idEntite
    this.rdvCreneauService.create(value).subscribe((res:any)=>{
      
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


  archive(){
    AlertNotif.finishConfirm("Suppression",
    "Cette action est irreversible. Voulez-vous continuer ?").then((result) => {
      if (result.value) {
      this.rdvCreneauService.delete(this.selected_data.id).subscribe((res:any)=>{
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
    this.rdvCreneauService.update(value,this.selected_data.id).subscribe((res)=>{
      this.modalService.dismissAll()
      this.init()
      AlertNotif.finish("Nouvelle modification",  "Motification effectué avec succès", 'success')
    }, (err)=>{
      AlertNotif.finish("Nouvelle modification", "Erreur, Verifiez que vous avez une bonne connexion internet", 'error')
    })
	}



}
