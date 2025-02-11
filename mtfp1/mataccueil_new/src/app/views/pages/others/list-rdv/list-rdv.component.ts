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
import { RdvService } from '../../../../core/_services/rdv.service';
import { LocalService } from '../../../../core/_services/browser-storages/local.service';


@Component({
  selector: 'app-list-rdv',
  templateUrl: './list-rdv.component.html',
  styleUrls: ['./list-rdv.component.css']
})
export class ListRdvComponent implements OnInit {

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
    this.data=[]
    this._temp=[]
    this.rdvService.getAll(this.user.idEntite,this.searchText,this.page).subscribe((res:any)=>{
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
    private rdvService:RdvService,
    private translate:TranslateService,
    private spinner: NgxSpinnerService,
    private localService : LocalService,
    private activatedRoute: ActivatedRoute,
    ) {}


  user:any
  ngOnInit() {

    if (localStorage.getItem('mataccueilUserData') != null) {
      this.user = this.localService.getJsonValue('mataccueilUserData')
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

    pager: any = {current_page: 0,
    data:[],
    last_page: 0,
    per_page: 0,
    to: 0,
    total: 0
  }
  subject = new Subject<any>();
  Null=null

  init(page){
    this._temp=[]
    this.data=[]
    if(this.user.agent_user!=null && this.user.profil_user.direction==1){
      this.rdvService.getAllByStructure(this.user.agent_user.idStructure,page).subscribe((res:any)=>{
        this.spinner.hide();
        res.data.forEach(e=>{
          e.check=false
          if(e.statut!=0){
            this.data.push(e)
          }
        })
        this._temp=this.data
        this.subject.next(res);
      })
    }else{
      this.rdvService.getAll(this.user.idEntite,null,page).subscribe((res:any)=>{
        this.spinner.hide();
        res.data.forEach(e=>{
          e.check=false
          if(e.statut!=0){
            this.data.push(e)
          }
        })
        this._temp=this.data
        this.subject.next(res);
      })
    }
  }
  
  setRdvStatut(pos){
    let checked=[]
    this.data.forEach(e=>{
      if(e.check==true){
        checked.push(e.id)
      }
    })
    if(checked.length==0){
      AlertNotif.finish("RDV", "Aucun élément selectionnés", 'error')
    }else{
      var msgConfirm = "Confirmation changement statut ?";
      var confirmResult = confirm(msgConfirm);
      if (confirmResult === false) return;
  
      var param = {
       listerdv: checked,
       statut: pos,
       idEntite:this.user.idEntite,
      }
      
      this.rdvService.saveRdvStatut(param).subscribe((res:any)=>{
        
        this.modalService.dismissAll()
        //this.translate.instant('HOME.TITLE')
        AlertNotif.finish("Prise de rdv","Les statut des rdv selectionnés on été modifié" , 'success')
         this.init(this.page) 
       },(err)=>{
        AlertNotif.finish("Prise de rdv", "Une erreur est survenue", 'error')
       }) 
  
    }
   
  }

  create(value){
    value.idEntite=this.user.idEntite
    this.rdvService.create(value).subscribe((res:any)=>{
      
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


  archive(id,index){
    AlertNotif.finishConfirm("Suppression",
    "Cette action est irreversible. Voulez-vous continuer ?").then((result) => {
      if (result.value) {
      this.rdvService.delete(id).subscribe((res:any)=>{
        this.data.splice(index,1)
        AlertNotif.finish("Suppression", "Suppression effectuée avec succès", 'success')
        this.init(this.page)
      }, (err)=>{
        AlertNotif.finish("Suppression", "Erreur, Verifiez que vous avez une bonne connexion internet", 'error')
      })
    }
   })
  }
  edit(value) {
    value.id=this.selected_data.id
    this.rdvService.update(value,this.selected_data.id).subscribe((res)=>{
      this.modalService.dismissAll()
      this.init(this.page)
      AlertNotif.finish("Nouvelle modification",  "Motification effectué avec succès", 'success')
    }, (err)=>{
      AlertNotif.finish("Nouvelle modification", "Erreur, Verifiez que vous avez une bonne connexion internet", 'error')
    })
	}

}
