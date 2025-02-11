import { Component, OnInit, Input } from '@angular/core';
import { PipeTransform } from '@angular/core';
import { DecimalPipe } from '@angular/common';
import { FormControl } from '@angular/forms';

import { Observable } from 'rxjs';
import { map, startWith } from 'rxjs/operators';
import {NgbModal, ModalDismissReasons} from '@ng-bootstrap/ng-bootstrap';
import { Router, ActivatedRoute } from '@angular/router';
import { UserService } from '../../../core/_services/user.service';

import { NgxSpinnerService } from 'ngx-spinner';
import { AlertNotif } from '../../../alert';
import { TranslateService } from '@ngx-translate/core';
import { User } from '../../../core/_models/user.model';
import { Roles } from '../../../core/_models/roles';
import { ProfilService } from '../../../core/_services/profil.service';
import { ActeurService } from '../../../core/_services/acteur.service';
import { Acteur } from '../../../core/_models/acteur.model';
import { LocalService } from '../../../core/_services/browser-storages/local.service';
import { InstitutionService } from '../../../core/_services/institution.service';


@Component({
  selector: 'app-users-main',
  templateUrl: './users-main.component.html',
  styleUrls: ['./users-main.component.css']
})
export class UsersMainComponent implements OnInit {

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
  selected_data:User

  search(){ 
    this.data=this._temp.filter(r => {
      const term = this.searchText.toLowerCase();
      return r.email.toLowerCase().includes(term) ||
      (r.agent_user==null ? '' : r.agent_user.nomprenoms).toLowerCase().includes(term) 
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
    private profilService:ProfilService,
    private acteursService:ActeurService,
    private translate:TranslateService,
    private spinner: NgxSpinnerService,
    private activatedRoute: ActivatedRoute,
    private institutionService:InstitutionService,
    private localStorageService : LocalService
    ) {}

    acteurs:any[]=[]
    profils:any[]=[]

  user:any
  ngOnInit() {
    if (localStorage.getItem('mataccueilUserData') != null) {
      this.user = this.localStorageService.getJsonValue("mataccueilUserData")

    }
    this.init()
  }

  institutions=[]

  init(){
    this._temp=[]
    this.data=[]
    this.userService.getAllMain().subscribe((res:any)=>{
      this.spinner.hide();
      this.data=res
      this._temp=this.data
      this.collectionSize=this.data.length
    })
    this.profils=[]
    this.profilService.getAllMain().subscribe((res:any)=>{
      this.profils=res
    })
    this.institutions=[]
    this.institutionService.getAll().subscribe((res:any)=>{
      this.institutions=res
    })
  
  }
  checked(event, el) {
    this.selected_data = el
  }
  

  loadActeur(event){
    this.acteurs=[]
    this.acteursService.getAll(+event.target.value).subscribe((res:any)=>{
      this.acteurs=res
    })
  }
  
  hide_actors=false

  changeProfil(event){
    let profil:any=this.profils.filter(e=>(+event.target.value==e.id))[0];
    if(profil.admin_sectoriel==1){
      this.hide_actors=true
    }else{
      this.hide_actors=false
    }
  }

  create(value){
    if(value.password==value.conf_password){
      this.userService.create(value).subscribe((res:any)=>{
      
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
    }else{
      this.error="Les deux mot de passe doivent être identique"
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
      this.userService.delete(this.selected_data.id).subscribe((res:any)=>{
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
    value.id=this.selected_data.id
    if(value.password!=value.conf_password){
      value.password=""
    }
    this.error="Le  mot de passe n'a pas été pris en compte car les deux ne sont pas identique"
    this.userService.update(value,this.selected_data.id).subscribe((res)=>{
      this.modalService.dismissAll()
      this.init()
      AlertNotif.finish("Nouvelle modification",  "Motification effectué avec succès", 'success')
    }, (err)=>{
      AlertNotif.finish("Nouvelle modification", "Erreur, Verifiez que vous avez une bonne connexion internet", 'error')
    })
	}


}
