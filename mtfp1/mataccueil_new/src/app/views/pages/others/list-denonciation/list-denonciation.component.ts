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
import { UsagerService } from '../../../../core/_services/usager.service';


@Component({
  selector: 'app-list-denonciation',
  templateUrl: './list-denonciation.component.html',
  styleUrls: ['./list-denonciation.component.css']
})
export class ListDenonciationComponent implements OnInit {

    
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
  selected_data:any

  search(){ 
    this.data=this._temp.filter(r => {
      const term = this.searchText.toLowerCase();
      return r.nom.toLowerCase().includes(term) || 
      r.prenoms.toLowerCase().includes(term) 
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

  openEditModal(content,el){
    this.selected_data=el
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
    private denonciationService:UsagerService,
    private translate:TranslateService,
    private spinner: NgxSpinnerService,
    private activatedRoute: ActivatedRoute,
    private localStorageService:LocalService
    ) {}

    // structures:[]=[]

    user:any
    ngOnInit() {
      if (localStorage.getItem('mataccueilUserData') != null) {
        this.user = this.localStorageService.getJsonValue("mataccueilUserData")
  
      }
     this.init()
    }
  init(){
    this._temp=[]
    this.data=[]
    this.denonciationService.getAllDenonciation().subscribe((res:any)=>{
      this.spinner.hide();
      this.data=res
      this._temp=this.data
      this.collectionSize=this.data.length
    })
   
  }
  
 

  archive(id,index){
    AlertNotif.finishConfirm("Suppression",
    "Cette action est irreversible. Voulez-vous continuer ?").then((result) => {
      if (result.value) {
      this.denonciationService.delete(id).subscribe((res:any)=>{
        this.data.splice(index,1)
        AlertNotif.finish("Suppression", "Suppression effectuée avec succès", 'success')
        this.init()
      }, (err)=>{
        AlertNotif.finish("Suppression", "Erreur, Verifiez que vous avez une bonne connexion internet", 'error')
      })
    }
   })
  }
  edit(value) {
    value.id=this.selected_data.id
    value.idEntite=this.user.idEntite
    this.denonciationService.update(value,this.selected_data.id).subscribe((res)=>{
      this.modalService.dismissAll()
      this.init()
      AlertNotif.finish("Nouvelle modification",  "Motification effectué avec succès", 'success')
    }, (err)=>{
      AlertNotif.finish("Nouvelle modification", "Erreur, Verifiez que vous avez une bonne connexion internet", 'error')
    })
	}


}
