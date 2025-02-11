import { Component, OnInit, Input } from '@angular/core';
import { PipeTransform } from '@angular/core';
import { DecimalPipe } from '@angular/common';
import { FormControl } from '@angular/forms';

import { Observable, Subject } from 'rxjs';
import { map, startWith } from 'rxjs/operators';
import { NgbModal, ModalDismissReasons } from '@ng-bootstrap/ng-bootstrap';
import { Router, ActivatedRoute, NavigationStart, NavigationEnd } from '@angular/router';
import { UserService } from '../../../../core/_services/user.service';

import { NgxSpinnerService } from 'ngx-spinner';
import { AlertNotif } from '../../../../alert';
import { TranslateService } from '@ngx-translate/core';
import { User } from '../../../../core/_models/user.model';
import { Roles } from '../../../../core/_models/roles';
import { RequeteService } from '../../../../core/_services/requete.service';
import { Event } from '../../../../core/_models/event.model';
import { EtapeService } from '../../../../core/_services/etape.service';
import { UsagerService } from '../../../../core/_services/usager.service';
import { ServiceService } from '../../../../core/_services/service.service';
import { StructureService } from '../../../../core/_services/structure.service';
import { LocalService } from '../../../../core/_services/browser-storages/local.service';
import { Config } from '../../../../app.config';
import { NatureRequeteService } from '../../../../core/_services/nature-requete.service';
import { ThemeService } from 'ng2-charts';
import { TypeService } from '../../../../core/_services/type.service';
import { InstitutionService } from '../../../../core/_services/institution.service';


@Component({
  selector: 'app-manage-requete-usager',
  templateUrl: './manage-requete-usager.component.html',
  styleUrls: ['./manage-requete-usager.component.css']
})
export class ManageRequeteUsagerComponent implements OnInit {

  

  @Input() cssClasses = '';
  errormessage = ""
  erroraffectation = ""

  searchText = ""
  closeResult = '';
  permissions: any[]
  error = ""
  data: any[] = [];
  _temp: any[] = [];
  collectionSize = 0;
  page = 1;
  pageSize = 10;

  selected = [];
  current_permissions: any[] = []
  selected_data: any
  isSended = false

  link_to_prestation=1
  selected_type_preoccupation=0

  search() {
    this.data = []
    this._temp = []
    this.requeteService.getAllForUser(this.user.idEntite,
      this.searchText,
      "true",this.user.id
      , this.page).subscribe((res: any) => {
        this.spinner.hide();
        this.data = res.data
        this.subject.next(res);
        this._temp = this.data
        this.collectionSize = this.data.length
      })

  }

  openAddModal(content) {
    if (this.selected_data != null) {
      this.modalService.open(content, { ariaLabelledBy: 'modal-basic-title', size: "lg" }).result.then((result) => {
        this.closeResult = `Closed with: ${result}`;
      }, (reason) => {
        this.closeResult = `Dismissed ${this.getDismissReason(reason)}`;
      });
    } else {
      AlertNotif.finish("Erreur", "Veuillez selectionnez un élément puis réessayer", 'error')
    }
  }
  openAddNewModal(content) {
      this.modalService.open(content, { ariaLabelledBy: 'modal-basic-title', size: "lg" }).result.then((result) => {
        this.closeResult = `Closed with: ${result}`;
      }, (reason) => {
        this.closeResult = `Dismissed ${this.getDismissReason(reason)}`;
      });
   
  }

  openEditModal(content, el) {
    this.selected_data = el
    this.modalService.open(content, { ariaLabelledBy: 'modal-basic-title' }).result.then((result) => {
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

  user: any

  constructor(
    private modalService: NgbModal,
    private userService: UserService,
    private router: Router,
    private translate: TranslateService,
    private institutionService:InstitutionService,
    private etapeService: EtapeService,
    private requeteService: RequeteService,
    private localService: LocalService,
    private prestationService: ServiceService,
    private structureService: StructureService,
    private natureService: NatureRequeteService,
    private thematiqueService: TypeService,
    private usagersService: UsagerService,
    private spinner: NgxSpinnerService,
    private activatedRoute: ActivatedRoute,
  ) { }


  institutions=[]
  etapes = []
  services = []
  __services=[]
  departements = []
  structureservices = []
  themes = []
  natures = []

  isGeneralDirector = false
  typeRequete = "requetes"

  checked(event, el) {
    this.selected_data = el
    if (this.selected_data.reponse.length > 0) {
      this.selected_data.reponse.forEach(item =>{
        if (item.typeStructure == 'SRU')
          this.selected_data.texteReponseApportee = item.texteReponse;

        if (item.typeStructure == 'SRU Secondaire')
          this.selected_data.reponseService = item.texteReponse;
      });
    }
   
   
    if(this.selected_data.service.hide_for_public == 1){
      this.selected_data.link_to_prestation=0
    }else{
      this.selected_data.link_to_prestation=1
    }
    console.log( this.selected_data)

  }
  show_step(id) {
    return this.etapes.find((e) => (e.id == id))
  }
  key_type_req = ""
  checkType() {
    this.key_type_req = this.activatedRoute.snapshot.paramMap.get('type_req')
    if (this.activatedRoute.snapshot.paramMap.get('type_req') == "plaintes") {
      return { id: 1, name: "Plaintes" }
    }
    if (this.activatedRoute.snapshot.paramMap.get('type_req') == "requetes") {
      return { id: 0, name: "Requetes" }
    }
    if (this.activatedRoute.snapshot.paramMap.get('type_req') == "infos") {
      return { id: 2, name: "Demandes d'informations" }
    }
  }

  ngOnInit(): void {
    this.prepare()
    
    this.router.events
    .subscribe(event => {
    
      if (event instanceof NavigationStart) {
        this.prepare()
      }
    })

  }

 prepare(){
  if (localStorage.getItem('mataccueilUserData') != null) {
    this.user = this.localService.getJsonValue('mataccueilUserData')
    if (this.user.profil_user.CodeProfil === 12) {
      this.isGeneralDirector = true;
    } else {
      this.isGeneralDirector = false;
    }
  }
  this.etapes = []
  this.etapeService.getAll(this.user.idEntite).subscribe((res: any) => {
    this.etapes = res
    this.activatedRoute.queryParams.subscribe(x => this.init(x.page || 1));
  })
  
    
  this.subject.subscribe((val) => {
 
    this.pager = val
    this.page = this.pager.current_page

    let pages = []
    if (this.pager.last_page <= 5) {
      for (let index = 1; index <= this.pager.last_page; index++) {
        pages.push(index)
      }
    } else {
      let start = (this.page > 3 ? this.page - 2 : 1)
      let end = (this.page + 2 < this.pager.last_page ? this.page + 2 : this.pager.last_page)
      for (let index = start; index <= end; index++) {
        pages.push(index)
      }
    }

    this.pager.pages = pages
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
  Null = null


  _temp2 = []
  data2 = []

  init(page) {

    this._temp = []
    this.data = []
    this.requeteService.getAllForUser(this.user.idEntite,
      null,
      "true",this.user.id
      , page).subscribe((res: any) => {
        this.spinner.hide();
        this.data = res.data
        this.subject.next(res);
        this._temp = this.data
        this.collectionSize = this.data.length
      })



    this.departements = []
    this.usagersService.getAllDepartement().subscribe((res: any) => {
      this.departements = res
    })
    this.services = []
    this.prestationService.getAll(this.user.idEntite).subscribe((res: any) => {
      this.services = res.filter(e=>(e.published==1))
      this.__services=this.services
    })

    this.structureservices = []
    this.structureService.getAllStructureByUser(this.user.id).subscribe((res: any) => {
      this.structureservices = res
    })
    this.natures = []
    this.natureService.getAll(this.user.idEntite).subscribe((res: any) => {
      this.natures = res
    })
    this.themes = []
    this.thematiqueService.getAll(this.user.idEntite).subscribe((res: any) => {
      this.themes = res
    })

    this.institutionService.getAll().subscribe((res: any) => {
      this.institutions = res
    })


  }

  saveAffectation(value) {
    let val = {
      idRequete: this.selected_data.id,
      idStructure: value.idStructure,
      idEntite:this.user.idEntite,
      listeemails: this.structureservices.find(e => (e.id == value.idStructure)).contact,
      typeStructure: 'SRU Secondaire',
      idEtape: 2,
    }
    if (this.selected_data.affectation.length != 1) {
      AlertNotif.finish("Erreur", "Cette requête a été déjà affectée.", 'error');
      return;
    }
    if(this.selected_data.reponse.length>0)
    {
      AlertNotif.finish("Erreur","Vous ne pouvez plus affecté cette requete car une réponse a été déjà proposée." , 'error') ;
        return;
    }
    this.requeteService.createAffectation(val).subscribe((res: any) => {
      this.init(this.page)
      this.modalService.dismissAll()
      AlertNotif.finish("Nouvelle affectation", "Affectation effectué avec succès", 'success')
    })
  }
  saveRequeteusager(value) {
    let service = null
    if ( this.selected_data.link_to_prestation==1) {
      service = this.services.filter(e => (e.id == value.idPrestation))[0]
    }else{
      service=this.services.filter(e => (e.hide_for_public == 1))[0]
    }
    var param = {
      id: this.selected_data.id,
      objet: this.selected_data.objet,
      idPrestation: this.selected_data.link_to_prestation==0 ? service.id : value.idPrestation,
      nbreJours: service == null ? 0 : service.nbreJours,
      msgrequest: this.selected_data.msgrequest,
      email: value.email,
      idEntite:this.user.idEntite,
      link_to_prestation: this.selected_data.link_to_prestation,
      nom: this.selected_data.nom,
      tel: this.selected_data.tel,
      idDepartement: this.selected_data.usager.idDepartement,
      interfaceRequete: this.selected_data.link_to_prestation == 1 ? "USAGER" : "SRU",
      natureRequete: value.natureRequete,
      idUser: this.selected_data.usager.id,
      plainte: value.plainte
    };
    this.requeteService.update(param, this.selected_data.id).subscribe((rest: any) => {
      this.init(this.page)
      this.modalService.dismissAll()
      AlertNotif.finish("Modification requete", "Requete modifié avec succès", 'success')
    })
  }
  chargerPrestation(event) {
    this.services=[]
    
    this.__services.forEach(item => {
      if (item.idType == event.target.value)
        this.services.push(item);
    });
  }
  genererPDF() {
    if (this.selected_data == null) {
      AlertNotif.finish("Erreur", "Veuillez selectionnez un élément puis réessayer", 'error');
      return;
    }
    var param = {
      id: this.selected_data.id,
    };
    this.requeteService.genPdf(param).subscribe((res: any) => {
      console.log('pdf generated')
    })

  }
  dropRequeteusager() {
    if (this.selected_data == null) {
      AlertNotif.finish("Erreur", "Veuillez selectionnez un élément puis réessayer", 'error');
      return;
    }
    if (this.selected_data.visible == 1) {
      AlertNotif.finish("Erreur", "Vous ne pouvez plus supprimer cette requête. Elle est déjà en cours de traitement.", 'error');
      return;
    }
    AlertNotif.finishConfirm("Suppression requete",
      "Cette action est irreversible. Voulez-vous continuer ?").then((result) => {
        if (result.value) {
          this.requeteService.delete(this.selected_data.id).subscribe((res: any) => {
            this.init(this.page)
            AlertNotif.finish("Suppression requete", "Suppression effectuée avec succès", 'success')
          }, (err) => {
            AlertNotif.finish("Suppression requete", "Erreur, Verifiez que vous avez une bonne connexion internet", 'error')
          })
        }
      })
  }
  displayResource() {
    if (this.selected_data == null) {
      AlertNotif.finish("Erreur", "Veuillez selectionnez un élément puis réessayer", 'error');
      return;
    }
    if (this.selected_data.fichier_joint.length == 0) {
      AlertNotif.finish("Erreur", "Aucun fichier attaché.", 'error');
      return;
    }
    var filePath = Config.toFile(this.selected_data.fichier_joint);
    window.open(filePath);
  }
  transmettreReponse(){
    if (this.selected_data == null) {
      AlertNotif.finish("Erreur", "Veuillez selectionnez un élément puis réessayer", 'error');
      return;
    }

    if(this.selected_data.finalise==1){
      AlertNotif.finish("Erreur","Réponse déjà transmise à l'usager." , 'error') ;
      return;
    }

    
    if(this.selected_data.reponse.length>0)
    {
      let check = this.selected_data.reponse.filter((item) => (item.typeStructure=='SRU'));
      if(check.length == 0){
        AlertNotif.finish("Erreur","Veuillez affecter ou traiter la préoccupation." , 'error') ;
      }
    }else{
      AlertNotif.finish("Erreur","Veuillez affecter ou traiter la préoccupation." , 'error') ;
    }


    if(this.selected_data.reponseStructure=='' || this.selected_data.reponseStructure==null)
    {
      AlertNotif.finish("Erreur","Veuillez valider la réponse de votre structure avant de transmettre.", 'error') ;
      return;
    }


    var msgConfirm = "Voulez-vous transmettre la réponse ?";
    var confirmResult = confirm(msgConfirm);
    if (confirmResult === false) return;
      var param = {
      idRequete: this.selected_data.id,
      typeStructure:'SRU',
      typeSuperieur:'Usager',
      idEntite:this.user.idEntite,
      idEtape:6,
    };
    this.requeteService.transmettreReponse(param).subscribe((rest:any)=>{
      this.init(this.page)
      this.modalService.dismissAll()
      AlertNotif.finish("Nouvelle réponse",  "Réponse envoyée et transmise avec succès", 'success')
    }) 
  }
  saveReponse(value){

    if(value.texteReponseApportee == null || value.texteReponseApportee == ""){
      AlertNotif.finish("Erreur", "Veuillez saisir votre réponse", 'error');
      return;
    }
    let complementReponse="";
    if(value.interrompu==true)
      complementReponse="\n\nRaison de l'interruption: "+"\n"+value.raisonRejet;
    else
    if(value.rejet==true)
      complementReponse="\n\nRaison du rejet: "+"\n"+value.raisonRejet;

    if(value.interrompu==true)
    if( value.texteReponseApportee.indexOf("Raison de l'interruption:")==-1)
      value.texteReponseApportee+=complementReponse;

    if(value.rejet==true)
    if( value.texteReponseApportee.indexOf("Raison du rejet:")==-1)
      value.texteReponseApportee+=complementReponse;

      var val = {
        idRequete:this.selected_data.id,
        typeStructure:'SRU',
        texteReponse: value.texteReponseApportee,
        interrompu: value.interrompu,
        idEntite:this.user.idEntite,
        rejete: value.rejete,
        raisonRejet : value.raisonRejet
     }; 
     this.requeteService.saveReponse(val).subscribe((res:any)=>{
      if(this.isSended){
        var paramInternal = {
          idRequete: this.selected_data.id,
          typeStructure:'SRU',
          typeSuperieur:'Usager',
          idEtape:6,
        };
        this.requeteService.transmettreReponse(paramInternal).subscribe((rest:any)=>{
          this.init(this.page)
          this.modalService.dismissAll()
          AlertNotif.finish("Nouvelle réponse",  "Réponse envoyée et transmise avec succès", 'success')
        }) 
      }else{
        this.init(this.page)
        this.modalService.dismissAll()
        AlertNotif.finish("Nouvelle réponse",  "Réponse envoyée avec succès", 'success')
      }
     
    }) 
  }
 
  structures=[]
  onEntiteChange(event){
 
    this.structures = []
    this.structureService.getAll(1,+event.target.value).subscribe((res:any)=>{
      this.structures = res
    })

    this.services = []
    this.__services=[]
    this.prestationService.getAll(+event.target.value).subscribe((res: any) => {
      this.services = res.filter(e=>(e.published==1))
      this.__services= this.services
    }) 

  }

  onStructureChange(event){
    this.services=[]
    this.__services.forEach(item => {
      if (item.idParent == event.target.value)
        this.services.push(item);
    });
  }
  transferPreocuppation(value){
    

    if (this.selected_data == null) {
      AlertNotif.finish("Erreur", "Veuillez selectionnez un élément puis réessayer", 'error');
      return;
    }

    if (this.selected_data.finalise == 1) {
      AlertNotif.finish("Erreur", "Réponse déjà transmise à l'usager.", 'error');
      return;
    }

    var param = {
      idStructure: value.idStructure,
      idEntiteReceive: value.idEntiteReceive,
      idPrestation: value.idPrestation,
    };
    AlertNotif.finishConfirm("Transférer cette préoccupation à un autre ministère/institution",
    "Cette action est irreversible. Voulez-vous continuer ?").then((result) => {
      if (result.value) {
        this.requeteService.transfertRequet(param,this.selected_data.id).subscribe((rest: any) => {
          this.init(this.page)
          this.modalService.dismissAll()
          AlertNotif.finish("Transfert préoccupation", "Transfert effectué avec succès", 'success')
        })
      }
    })
}
relancerPreocuppation(){
  

  if (this.selected_data == null) {
    AlertNotif.finish("Erreur", "Veuillez selectionnez un élément puis réessayer", 'error');
    return;
  }

  if (this.selected_data.finalise == 1) {
    AlertNotif.finish("Erreur", "Réponse déjà transmise à l'usager.", 'error');
    return;
  }

  this.requeteService.relanceRequet(this.selected_data.id).subscribe((rest: any) => {
    this.init(this.page)
    this.modalService.dismissAll()
    AlertNotif.finish("Relancer la structure en charge de la préoccupation", "Relance envoyée avec succès", 'success')
  })
   
}
searchEmail=""
usagerEmailFind=0
selectedUsager:any
findUsager(){
  
  if(this.searchEmail==""){
    this.usagerEmailFind=0
  }else{
    this.usagerEmailFind=1
    
  }
  this.usagersService.getAll(this.searchEmail,this.page).subscribe((res:any)=>{
    console.log(res.data)
    if(res.data.length==1){
      this.usagerEmailFind=2
      this.selectedUsager=res.data[0]
    }else{
      this.usagerEmailFind=3
    }
  })
}

addRequeteusager(value){
  let service = null
  if (this.link_to_prestation==1 || this.selected_type_preoccupation==0) {
    service = this.services.filter(e => (e.id == value.idPrestation))[0]
  }else{
    service=this.services.filter(e => (e.hide_for_public == 1))[0]
  }
  var param = {
    objet: value.objet,
    idPrestation: this.link_to_prestation==0  ? service.id : value.idPrestation,
    nbreJours: service == null ? 0 : service.nbreJours,
    msgrequest: value.msgrequest,
    link_to_prestation:this.link_to_prestation,
    email:this.selectedUsager.email,
    nom:this.selectedUsager.nom,
    tel:this.selectedUsager.tel,
    idDepartement:this.selectedUsager.idDepartement,
    interfaceRequete: "SRU" ,
    idUser:this.user.id,
    plainte: value.plainte,
    visible:1
 };
 this.requeteService.create(param).subscribe((rest:any)=>{
  this.modalService.dismissAll()
  this.init(this.page)
  AlertNotif.finish("Ajout requête",  "Requête ajoutée avec succès", 'success')
})     
}
}
