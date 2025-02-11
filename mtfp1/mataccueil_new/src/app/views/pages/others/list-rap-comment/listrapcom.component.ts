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
import { EtapeService } from '../../../../core/_services/etape.service';
import { CommentaireService } from '../../../../core/_services/commentaire.service';
import { Etape } from '../../../../core/_models/etape.model';
import { LocalService } from '../../../../core/_services/browser-storages/local.service';
import { Config } from '../../../../app.config';

@Component({
  selector: 'app-listrapcom',
  templateUrl: './listrapcom.component.html',
  styleUrls: ['./listrapcom.component.css']
})
export class RapCommentComponent implements OnInit {

  @Input() cssClasses = '';
  page = 1;
  pageSize = 50;
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
  dated:''
  datef:''

  search(){ 
    this.data=this._temp.filter(r => {
      const term = this.searchText.toLowerCase();
      return r.commentaire.toLowerCase().includes(term) || r.num_enreg.toLowerCase().includes(term) 
    })
    this.collectionSize=this.data.length
  }
  print_rapp(libdate){
    var url= Config.toApiUrl('rapportconsult?date='+libdate)
    window.open(url, "_blank")  
  }
  print_rapp_periode(){
    if(this.dated && this.datef){
      var url= Config.toApiUrl('rapportconsult?date='+this.dated+'&datef='+this.datef+'&idEntite='+this.user.idEntite)
      window.open(url, "_blank")  
    }else{
      AlertNotif.finish("Error", "Date début et fin sont obligatoire", 'error')
    }
  }
  print_graphe_periode(){
    if(this.dated && this.datef){
      var url= Config.toApiUrl('rapportGraph?date='+this.dated+'&datef='+this.datef+'&idEntite='+this.user.idEntite)
      window.open(url, "_blank")  
    }else{
      AlertNotif.finish("Error", "Date début et fin sont obligatoire", 'error')
    }
  }

  graphe_rapp(libdate){
    var url= Config.toApiUrl('rapportGraph?date='+libdate)
    window.open(url, "_blank")  
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
    // private etapeService:EtapeService,
    private commentaireService:CommentaireService,
    private translate:TranslateService,
    private spinner: NgxSpinnerService,
    private activatedRoute: ActivatedRoute,
    private localStorageService : LocalService
    ) {}


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
    this.commentaireService.getAll().subscribe((res:any)=>{
      this.spinner.hide();
      this.data=res
      this._temp=this.data
      this.collectionSize=this.data.length
    })
  }
    checked(event, el) {
      console.log(this.user,el)
      this.selected_data = el
  }

  ChangerFile(file){
    // window.location.href="http://api.mataccueil.sevmtfp.test/api/downloadFileCom?file="+file
    window.location.href="https://api.mataccueil.gouv.bj/api/downloadFileCom?file="+file
    // window.location.href="http://localhost:8003/api/downloadFileCom?file="+file
  }
  
  file: string | Blob =""
  onFileChange(event:any) {
    if (event.target.files.length > 0) {
      this.file = event.target.files[0];
    }
  }

  create(value){

    let formData = new FormData()
    formData.append('date_debut_com', value.datedebut)
    formData.append('date_fin_com', value.datefin)
    formData.append('commentaire', value.comment)
    formData.append('fichier', this.file)
    formData.append('id_init', this.user.id)

    this.commentaireService.create(formData).subscribe((res:any)=>{
      if(res.status == 'error'){
        AlertNotif.finish("Nouvel ajout",res.message , 'error')
      }else{
        this.modalService.dismissAll()
        AlertNotif.finish("Nouvel ajout","Ajout effectué avec succès" , 'success')
         this.init() 
      }
    },(err)=>{
      if(err.error.detail!=null){    
        AlertNotif.finish("Nouvel ajout", err.error.detail, 'error')
      }else{
        AlertNotif.finish("Nouvel ajout", "Erreur, Verifiez que vous avez une bonne connexion internet", 'error')
      }
    })
  }

  edit(value) {
    
    let formData = new FormData()
    formData.append('date_debut_com', value.datedebut)
    formData.append('date_fin_com', value.datefin)
    formData.append('id_comment', this.selected_data.id_comment)
    formData.append('commentaire', value.comment)

    this.commentaireService.update(formData,this.selected_data.id_comment).subscribe((res)=>{
      if(res.status == 'error'){
        AlertNotif.finish("Nouvelle modification",res.message , 'error')
      }else{
        this.modalService.dismissAll()
        AlertNotif.finish("Nouvelle modification",  "Motification effectué avec succès", 'success')
         this.init() 
      }
    }, (err)=>{
      AlertNotif.finish("Nouvelle modification", "Erreur, Verifiez que vous avez une bonne connexion internet", 'error')
    })
	}

  archive(){
    if (this.selected_data == null) {
      AlertNotif.finish("Erreur", "Veuillez selectionnez un élément puis réessayer", 'error');
      return;
    }
    AlertNotif.finishConfirm("Suppression",
    "Cette action est irreversible. Voulez-vous continuer ?").then((result) => {
      if (result.value) {
      this.commentaireService.delete(this.selected_data.id_comment).subscribe((res:any)=>{
      
        AlertNotif.finish("Suppression", "Suppression effectuée avec succès", 'success')
        this.init()
      }, (err)=>{
        AlertNotif.finish("Suppression", "Erreur, Verifiez que vous avez une bonne connexion internet", 'error')
      })
    }
   })
  }
  

}
