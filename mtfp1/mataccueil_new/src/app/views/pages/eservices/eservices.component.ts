import { Component, Input, OnInit } from '@angular/core';
import { Router, ActivatedRoute } from '@angular/router';
import { ModalDismissReasons, NgbModal } from '@ng-bootstrap/ng-bootstrap';
import { TranslateService } from '@ngx-translate/core';
import { NgxSpinnerService } from 'ngx-spinner';
import { AlertNotif } from 'src/app/alert';
import { User } from 'src/app/core/_models/user.model';
import { ActeurService } from 'src/app/core/_services/acteur.service';
import { LocalService } from 'src/app/core/_services/browser-storages/local.service';
import { EserviceService } from 'src/app/core/_services/eservice.service';
import { ProfilService } from 'src/app/core/_services/profil.service';
import { UserService } from 'src/app/core/_services/user.service';

@Component({
  selector: 'app-eservices',
  templateUrl: './eservices.component.html',
  styleUrls: ['./eservices.component.css']
})
export class EservicesComponent implements OnInit {

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

  is_active:any
  current_permissions:any[]=[]
  collectionSize = 0;
  selected_data:any
  is_external_service=false

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
    private eservices: EserviceService,
    private router:Router,
    private translate:TranslateService,
    private spinner: NgxSpinnerService,
    private activatedRoute: ActivatedRoute,
    private localStorageService : LocalService
    ) {}

    acteurs:[]=[]
    profils:[]=[]

  user:any
  ngOnInit() {
    if (localStorage.getItem('mataccueilUserData') != null) {
      this.user = this.localStorageService.getJsonValue("mataccueilUserData")

    }
    this.init()
  }
  checked(event, el) {
    this.selected_data = el
    this.is_active=el.is_published
  }
  
  init(){
    this._temp=[]
    this.data=[]
    this.eservices.getAll().subscribe((res:any)=>{
      this.spinner.hide();
      this.data=res.data
      this._temp=this.data
      this.collectionSize=this.data.length
    })
   
  }
  
  create(value){
      this.eservices.create(value).subscribe((res:any)=>{
      
        this.modalService.dismissAll()
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
    if (this.selected_data == null) {
      AlertNotif.finish("Erreur", "Veuillez selectionnez un élément puis réessayer", 'error');
      return;
    }
    AlertNotif.finishConfirm("Suppression",
    "Cette action est irreversible. Voulez-vous continuer ?").then((result) => {
      if (result.value) {
      this.eservices.delete(this.selected_data.id).subscribe((res:any)=>{
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
    if(value.password!=value.conf_password){
      value.password=""
    }
    value.idEntite=this.user.idEntite
    this.eservices.update(value,this.selected_data.id).subscribe((res)=>{
      this.modalService.dismissAll()
      this.init()
      AlertNotif.finish("Nouvelle modification",  "Motification effectué avec succès", 'success')
    }, (err)=>{
      AlertNotif.finish("Nouvelle modification", "Erreur, Verifiez que vous avez une bonne connexion internet", 'error')
    })
	}


  setState(value){
    this.eservices.setState(this.selected_data?.id,value).subscribe((res:any)=>{
    
      this.modalService.dismissAll()
      AlertNotif.finish("Nouvel MAJ","MAJ effectué avec succès" , 'success')
       this.init() 
     },(err)=>{
       
       if(err.error.detail!=null){    
         AlertNotif.finish("Nouvel MAJ", err.error.detail, 'error')
       }else{
         AlertNotif.finish("Nouvel MAJ", "Erreur, Verifiez que vous avez une bonne connexion internet", 'error')
       }
     })

  
}

}
