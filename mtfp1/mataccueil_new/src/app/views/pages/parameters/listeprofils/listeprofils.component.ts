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
import { ProfilService } from '../../../../core/_services/profil.service';
import { Profil } from '../../../../core/_models/profil.model';



@Component({
  selector: 'app-listeprofils',
  templateUrl: './listeprofils.component.html',
  styleUrls: ['./listeprofils.component.css']
})
export class ListeprofilsComponent implements OnInit {

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
  selected_data:Profil

  file: string | Blob =""
  onFileChange(event:any) {
    if (event.target.files.length > 0) {
      this.file = event.target.files[0];
    }
  }
  search(){ 
    this.data=this._temp.filter(r => {
      const term = this.searchText.toLowerCase();
      return r.LibelleProfil.toLowerCase().includes(term) 
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

  ChangerFile(file){
    window.location.href="https://api.mataccueil.gouv.bj/api/downloadFileGuide?file="+file
    // window.location.href="http://localhost:8003/api/downloadFileGuide?file="+file
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
    private translate:TranslateService,
    private spinner: NgxSpinnerService,
    private activatedRoute: ActivatedRoute,
    ) {}


  ngOnInit() {
    
   this.init()
  }
  init(){
    this._temp=[]
    this.data=[]
    this.profilService.getAll().subscribe((res:any)=>{
      this.spinner.hide();
      this.data=res
      this._temp=this.data
      this.collectionSize=this.data.length
    })
  }
    checked(event, el) {
    this.selected_data = el
  }
  
  create(value){
    this.profilService.create(value).subscribe((res:any)=>{
      
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
  
  addGuide(value){

    let formData = new FormData()
    formData.append('fichier', this.file)

    this.profilService.addGuideUser(formData,this.selected_data.id).subscribe((res:any)=>{
      if(res.status == 'error'){
        AlertNotif.finish("Ajout guide",res.message , 'error')
      }else{
        this.modalService.dismissAll()
        AlertNotif.finish("Ajout guide","Guide ajouté avec succès" , 'success')
         this.init() 
      }
     
    },(err)=>{
      
      if(err.error.detail!=null){    
        AlertNotif.finish("Ajout guide", err.error.detail, 'error')
      }else{
        AlertNotif.finish("Ajout guide", "Erreur, Verifiez que vous avez une bonne connexion internet", 'error')
      }
    })
  }


  archive(){
    AlertNotif.finishConfirm("Suppression",
    "Cette action est irreversible. Voulez-vous continuer ?").then((result) => {
      if (result.value) {
      this.profilService.delete(this.selected_data.id).subscribe((res:any)=>{
        this.init()
        AlertNotif.finish("Suppression", "Suppression effectuée avec succès", 'success')
      }, (err)=>{
        AlertNotif.finish("Suppression", "Erreur, Verifiez que vous avez une bonne connexion internet", 'error')
      })
    }
   })
  }
  edit(value) {
    value.code=this.selected_data.CodeProfil
    value.id=this.selected_data.id
    this.profilService.update(value,this.selected_data.id).subscribe((res)=>{
      this.modalService.dismissAll()
      this.init()
      AlertNotif.finish("Nouvelle modification",  "Motification effectué avec succès", 'success')
    }, (err)=>{
      AlertNotif.finish("Nouvelle modification", "Erreur, Verifiez que vous avez une bonne connexion internet", 'error')
    })
	}


}
