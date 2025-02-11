import { Component, OnInit } from '@angular/core';
import {AuthService} from '../../core/_services/auth.service';
import {AlertNotif} from '../../alert';
import { Usager } from '../../core/_models/usager.model';
import {NgbModal, ModalDismissReasons} from '@ng-bootstrap/ng-bootstrap';
import { Observable, Subject } from 'rxjs';
import {clientData, globalName} from '../../core/_utils/utils';
import {LocalService} from '../../core/_services/storage_services/local.service';
import { Subscription } from 'rxjs';
import { Config } from '../../app.config';
import { ActivatedRoute, Router } from '@angular/router';

@Component({
  selector: 'app-homepfc',
  templateUrl: './homepfc.component.html',
  styleUrls: ['./homepfc.component.css']
})
export class HomepfcComponent implements OnInit {

  loadFile(file){
    return Config.toFile(file)
  }
  page = 1;
  

  subs:Subscription

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
    private user_auth_service:AuthService, 
    private  local_service:LocalService,
    private router:Router,
    private modalService: NgbModal,
    private localStorageService:LocalService,
    private activatedRoute: ActivatedRoute,
    ) { 
    if(localStorage.getItem(globalName.current_user)!=undefined) this.user=this.local_service.getItem('matUserData');
  }
  loading:boolean=false
  id:any
  data:any
  user:any
  access_token:any
  _temp: any[]=[];
  
  subject = new Subject<any>();
  pager: any = {current_page: 0,
    data:[],
    last_page: 0,
    per_page: 0,
    to: 0,
    total: 0
  }
  link_to_prestation=1
  selected_type_preoccupation=0
  error=""
  searchText=""
  
  selected_data:Usager
  closeResult = '';
  departements:[]=[]
  detailpiece=[]
  structures=[]
  natures=[]
  dataNT: any[] = [];
  themes=[]
  services=[]
  descrCarr=[]

  ngOnInit(): void {
    if (localStorage.getItem('matUserData') != null) {
      this.user = this.localStorageService.getJsonValue("matUserData")
    }
        console.log(this.user)
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

  search(){ 
    this.data=[]
    this._temp=[]
    this.user_auth_service.getAll(this.searchText,this.page).subscribe((res:any)=>{
      // this.spinner.hide();
      // user_auth_service
      this.data=res.data
      this._temp=this.data
      this.subject.next(res);
    })
  }
  checked(event, el) {
    this.selected_data = el
    console.log(el)
    
    this.user_auth_service.getAllForUsagerNT(el.id,1).subscribe((res: any) => {
      this.dataNT = res
    })
    
  }
  openAddModal(content) {
    this.modalService.open(content, {ariaLabelledBy: 'modal-basic-title'}).result.then((result) => {
      this.closeResult = `Closed with: ${result}`;
    }, (reason) => {
      this.closeResult = `Dismissed ${this.getDismissReason(reason)}`;
    });
  }

  createUsager(value){
    if(value.idDepartement == ""){
      // this.error="Les deux mot de passe doivent être identique"
      AlertNotif.finish("Nouvel ajout", "Sélectionner le département", 'error')
    }else if(value.password!=value.conf_password && value.password != ""){
      AlertNotif.finish("Nouvel ajout", "Les deux mot de passe doivent être identique", 'error')
    }else{
      this.user_auth_service.createUsager(value).subscribe((res:any)=>{
      
        this.modalService.dismissAll()
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

  editUsager(value) {
    value.id=this.selected_data.id
    value.code=this.selected_data.code
    value.codeComplet=this.selected_data.codeComplet
    if(value.password!=value.conf_password){
      value.password=""
      this.error="Le  mot de passe n'a pas été pris en compte car les deux ne sont pas identique"
    }else{
      this.user_auth_service.updateUsager(value,this.selected_data.id).subscribe((res)=>{
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
    this.user_auth_service.getAllTypePrest(event.target.value).subscribe((res:any)=>{
      this.services=res
    })
    
    this.user_auth_service.getThema(event.target.value).subscribe((res:any)=>{
      this.descrCarr = res.descr
    })
    
  }

  addRequeteusager(value){
    let service = null
    // if (this.link_to_prestation==1 || this.selected_type_preoccupation==0) {
    if (this.link_to_prestation==1) {
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
        idPrestation: this.link_to_prestation==0  ? '435' : value.idPrestation, //cas d'une requete
        nbreJours: service == null ? 0 : service.nbreJours,
        msgrequest: value.msgrequest,
        email:this.selected_data.email,
        nom:this.selected_data.nom,
        tel:this.selected_data.tel,
        idDepartement:this.selected_data.idDepartement,
        interfaceRequete: this.link_to_prestation==1 ? "USAGER"  : "SRU" ,
        idUser:this.user.id,
        idEntite:this.user.idEntite,
        plainte: value.plainte,
        visible:1
     };

     this.user_auth_service.createrequeteusager(param).subscribe((rest:any)=>{
      this.modalService.dismissAll()
      AlertNotif.finish("Ajout requête",  "Requête ajoutée avec succès", 'success')
    })     

    }
  }

  openDetailModal(event,content){

    this.detailpiece=[]
    this.user_auth_service.getServPiece(event.target.value).subscribe((res:any)=>{
      this.detailpiece=res
    })
    
    this.modalService.open(content, {ariaLabelledBy: 'modal-basic-title'}).result.then((result) => {
      this.closeResult = `Closed with: ${result}`;
    }, (reason) => {
      this.closeResult = `Dismissed ${this.getDismissReason(reason)}`;
    });
  }

  setNotif(){
    this.user_auth_service.setNotif({
    }).subscribe(
      (res:any)=>{
             
      },
      (err)=>{
          this.loading=false;
          console.log(err)
         // AlertNotif.finish("Applications","Echec de récupération des applications","error")
        }
  )
  }

  init(page){
    this.loadRequest()

    this._temp=[]
    this.data=[]
    this.user_auth_service.getAll(null,page).subscribe((res:any)=>{
      // this.spinner.hide();
      this.data=res.data
      this._temp=this.data
      this.subject.next(res);
    })
 
    this.departements=[]
    this.user_auth_service.getAllDepartement().subscribe((res:any)=>{
      this.departements=res
    })

 
    this.structures = []
    this.user_auth_service.getAllServ(1,this.user.idEntite).subscribe((res:any)=>{
      this.structures = res
    })

   this.natures=[]
    this.user_auth_service.getAllNatu(this.user.idEntite).subscribe((res:any)=>{
      this.natures=res
    })

    this.themes=[]
    this.user_auth_service.getAllThe(this.user.idEntite).subscribe((res:any)=>{
      this.themes=res
    })

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
  
  openEditModalLg(content){
    if (this.selected_data == null) {
      AlertNotif.finish("Erreur", "Veuillez selectionnez un élément puis réessayer", 'error');
      return;
    }
    this.modalService.open(content, {ariaLabelledBy: 'modal-basic-title', size: 'lg'}).result.then((result) => {
      this.closeResult = `Closed with: ${result}`;
    }, (reason) => {
      this.closeResult = `Dismissed ${this.getDismissReason(reason)}`;
    });
  }
// --------------------------------------------------------_______________________-------------- LISTE DES REQUETES 
searchTextReq=""
show_actions=true;
selected_data_req: any
selectedEntie=null
pageSize = 10;
collectionSize = 0;
dataReq:any
etapes = []
_tempReq: any[]=[];
pageReq = 1;

searchReq() {
  this.dataReq = this._temp.filter(r => {
    const term = this.searchTextReq.toLowerCase();
    return r.objet.toLowerCase().includes(term) ||
    r.msgrequest.toLowerCase().includes(term)
  })
  this.collectionSize = this.dataReq.length
}
dropRequeteusager() {
  if (this.selected_data_req == null) {
    AlertNotif.finish("Erreur", "Veuillez selectionnez un élément puis réessayer", 'error');
    return;
  }
  if (this.selected_data_req.visible == 1) {
    AlertNotif.finish("Erreur", "Vous ne pouvez plus supprimer cette requête. Elle est déjà en cours de traitement.", 'error');
    return;
  }
  AlertNotif.finishConfirm("Suppression requete",
    "Cette action est irreversible. Voulez-vous continuer ?").then((result) => {
      if (result.value) {
        this.user_auth_service.deleteReq(this.selected_data_req.id).subscribe((res: any) => {
          this.loadRequest()
          AlertNotif.finish("Suppression requete", "Suppression effectuée avec succès", 'success')
        }, (err) => {
          AlertNotif.finish("Suppression requete", "Erreur, Verifiez que vous avez une bonne connexion internet", 'error')
        })
      }
    })
}

loadRequest() {
  this._tempReq = []
  this.dataReq = []
  // this.dataNTreq = []
  
  
  this.user_auth_service.getAllForPfcom(this.user.id
    , 1).subscribe((res: any) => {
      this.dataReq = res
      this._tempReq = this.dataReq
      this.collectionSize = this.dataReq.length
    })
    this.ChechEtape();
}

ChechEtape(){
  this.etapes = []
  this.user_auth_service.getAllEtap(0).subscribe((res: any) => {
    this.etapes = res
  })
}

checkedReq(event, el) {
  this.selected_data_req = el

  console.log('++++++++++++++++++++++++++++++++')
  console.log(this.selected_data_req)
  if(el.visible==0){
    this.show_actions=true
  }else{
    this.show_actions=false
  }
  this.selectedEntie=el.idEntite
  
  this.user_auth_service.getAllThe(this.selected_data_req.idEntite).subscribe((res: any) => {
    this.themes = res
  })
  this.user_auth_service.getThema(this.selected_data_req).subscribe((res:any)=>{
    this.descrCarr = res.descr
  })
  this.user_auth_service.getAllTypePrest(this.selected_data_req.service.idType).subscribe((res:any)=>{
    this.services=res
  })
}

}
