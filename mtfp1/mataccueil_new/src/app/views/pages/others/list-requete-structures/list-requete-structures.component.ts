import { Component, OnInit, Input } from '@angular/core';
import { PipeTransform } from '@angular/core';
import { DecimalPipe } from '@angular/common';
import { FormControl } from '@angular/forms';

import { Observable, Subject } from 'rxjs';
import { map, startWith } from 'rxjs/operators';
import { NgbModal, ModalDismissReasons } from '@ng-bootstrap/ng-bootstrap';
import { Router, ActivatedRoute, NavigationStart } from '@angular/router';
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
import { InstitutionService } from '../../../../core/_services/institution.service';


@Component({
  selector: 'app-list-requete-structures',
  templateUrl: './list-requete-structures.component.html',
  styleUrls: ['./list-requete-structures.component.css']
})
export class ListRequeteStructuresComponent implements OnInit {

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

  data2: any[] = [];
  _temp2: any[] = [];
  collectionSize2 = 0;
  page2 = 1;
  pageSize2 = 10;

  selected = [];
  current_permissions: any[] = []
  selected_data: any
  isSended = false

  search() {
    this.data = []
    this._temp = []
    this.requeteService.getAllRequest(this.user.idEntite,this.searchText, 0, this.user.id, this.user.agent_user.idStructure,
      this.checkType().id, this.page).subscribe((res: any) => {
        this.spinner.hide();
        // this.data = res.data;
        this.data = res.data.filter(e=>{
          if(e.lastparcours != null){
            return (e.lastparcours.idEtape==1) || 
                      (e.lastparcours.idEtape==5) || 
                      (e.lastparcours.idEtape==7 && e.lastparcours.idStructure == this.user.agent_user.idStructure) ||
                      (e.lastparcours.idEtape==8 && e.lastparcours.idEntite == this.user.idEntite);
          }else{
            return (e.lastparcours == null);
          }
        })
        this._temp = this.data
        this.subject.next(res);
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

  openEditModal(content, el) {
    this.selected_data = el
    this.modalService.open(content, { ariaLabelledBy: 'modal-basic-title' }).result.then((result) => {
      this.closeResult = `Closed with: ${result}`;
    }, (reason) => {
      this.closeResult = `Dismissed ${this.getDismissReason(reason)}`;
    });
  }

  private getDismissReason(reason: any): string {
    // this.selected_data=null
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
    private usagersService: UsagerService,
    private spinner: NgxSpinnerService,
    private activatedRoute: ActivatedRoute,
  ) { }


  etapes = []
  services = []
  departements = []
  structureservices = []

  isGeneralDirector = false
  typeRequete = "requetes"
  usager_full_name=""
  hide_actions=false
  action_transmettre = true
  RelanceAWho = ""
  ValStruRelance = ""
  cpt = 0
  compteData = 0
  
  checked(event, el) {
    this.selected_data = el
    if(this.selected_data.usager == null){
      this.usager_full_name=" (PFC) "+this.selected_data.email+" Contact : "+this.selected_data.contact
    }else{
      this.usager_full_name=this.selected_data.usager.nom+" "+this.selected_data.usager.prenoms
    }
    console.log(this.selected_data)
    // console.log(this.user)
    // console.log(this.user)
    if (this.selected_data.reponse.length > 0) {
      this.selected_data.reponse.forEach(item => {
        if (item.typeStructure == 'Direction')
          this.selected_data.texteReponseApportee = item.texteReponse;

        if (item.typeStructure == 'Service')
          this.selected_data.reponseService = item.texteReponse;
      });
    }
    this.action_transmettre = true
    if (this.selected_data.reponse.length > 0) {
      let check = this.selected_data.reponse.filter((item) => (item.typeStructure=='Direction'));
      if(check.length == 0){
        // AlertNotif.finish("Erreur","Veuillez affecter ou traiter la préoccupation." , 'error') ;
        this.action_transmettre = false
      }
    }else{
      // AlertNotif.finish("Erreur", "Veuillez affecter ou traiter la préoccupation.", 'error');
      this.action_transmettre = false
    }

    this.hide_actions=false
    if (this.selected_data.affectation.length > 0) {
      this.selected_data.affectation.forEach(item => {
        if (item.typeStructure == 'Service'){ this.hide_actions=true;}
      })

    }

    this.RelanceAWho = ""
    this.ValStruRelance = ""
    this.cpt = 0
    if (this.selected_data.affectation.length > 0) {
      this.selected_data.affectation.forEach(item => {
        this.cpt++;
        // console.log("Cpt : "+this.cpt,"Nombre : "+this.selected_data.affectation.length,"itemStruc : "+item.idStructure,"UserStructure : "+this.user.agent_user.idStructure)
        if (this.cpt == this.selected_data.affectation.length && item.idStructure != this.user.agent_user.idStructure){
             this.RelanceAWho = item.typeStructure;
             this.ValStruRelance = item.idStructure;
          }
      })
    }
    this.cpt = 0
    if (this.selected_data.parcours.length > 0) {
      this.selected_data.parcours.forEach(item => {
        this.cpt++;
        if (this.cpt == this.selected_data.parcours.length && item.idStructure == this.user.agent_user.idStructure){
          this.RelanceAWho = ""
          this.ValStruRelance = ""
          }
      })
    }
  }
  relancerPreocuppationType(){
    if (this.selected_data == null) {
      AlertNotif.finish("Erreur", "Veuillez selectionnez un élément puis réessayer", 'error');
      return;
    }
    this.cpt = 0
    if (this.selected_data.parcours.length > 0) {
      this.selected_data.parcours.forEach(item => {
        this.cpt++;
        // console.log("Cpt : "+this.cpt,"Nombre : "+this.selected_data.parcours.length,"itemStruc : "+item.idStructure,"UserStructure : "+this.user.agent_user.idStructure)
        if (this.cpt == this.selected_data.parcours.length && item.idStructure == this.user.agent_user.idStructure){
            AlertNotif.finish("Erreur", "Impossible de faire une relance car le responsable structure a déjà donnée sa réponse", 'error');
            return;
          }
      })
    }

    if (this.selected_data.finalise == 1) {
      AlertNotif.finish("Erreur", "Réponse déjà transmise à l'usager.", 'error');
      return;
    }
    if(this.ValStruRelance == ""){
      AlertNotif.finish("Erreur", "Impossible de relancer sur cette requête.", 'error');
      return;
    }else{
      this.requeteService.relanceRequetType(this.selected_data.id, this.ValStruRelance,this.selected_data.lastaffectation == null ? '' : this.selected_data.lastaffectation.idStructure).subscribe((rest: any) => {
        if(rest.status == "error"){
          AlertNotif.finish("Erreur",rest.message, 'error');
        }else{
          this.init(this.page)
          this.modalService.dismissAll()
          AlertNotif.finish("Relancer "+this.RelanceAWho+" en charge de la préoccupation", "Relance envoyée avec succès à l'adresse : "+rest.message, 'success')
          this.selected_data = null
        }
      })

    }
    
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
    this.RelanceAWho = ""
    this.router.events
      .subscribe(event => {

        if (event instanceof NavigationStart) {
          this.prepare()
        }
      })
  }
  prepare() {
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
    
    this.typeRequete = this.checkType().name;
    this.subject.subscribe((val) => {
      this.typeRequete = this.checkType().name;
      
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
      this.compteData = this.pager.total
    });
  }

  pager: any = {
    current_page: 0,
    data: [],
    last_page: 0,
    per_page: 0,
    to: 0,
    total: 0
  }
  subject = new Subject<any>();
  Null = null

  institutions=[]
  _cpt = 0;
  _data_affect = 0


  init(page) {

    this._temp = []
    this.data = []
    this.requeteService.getAllRequest(this.user.idEntite,null, 0, this.user.id, this.user.agent_user.idStructure, this.checkType().id, page).subscribe((res: any) => {
        this.spinner.hide();
        this.subject.next(res);
        // this.data = res.data;
        this.data = res.data.filter(e=>{
          if(e.lastparcours != null){
            return (e.lastparcours.idEtape==1) || 
                      (e.lastparcours.idEtape==5) || 
                      (e.lastparcours.idEtape==7 && e.lastparcours.idStructure == this.user.agent_user.idStructure) ||
                      (e.lastparcours.idEtape==8 && e.lastparcours.idEntite == this.user.idEntite);
          }else{
            return (e.lastparcours == null);
          }
        })
        this._temp = this.data
      })
    this._temp2 = []
    this.data2 = []
    this.requeteService.getAllAffectation(this.user.id, "Service", this.checkType().id, page).subscribe((res: any) => {
      this.spinner.hide();
      if (Array.isArray(res)) {
        this.data2 = res
      } else {
        this.data2 = res.data
      }
      this._temp2 = this.data2
    })
    this.departements = []
    this.usagersService.getAllDepartement().subscribe((res: any) => {
      this.departements = res
    })
    this.services = []
    this.__services=[]
    this.prestationService.getAll(this.user.idEntite).subscribe((res: any) => {
      this.services = res.filter(e=>(e.published==1))
      this.__services= this.services
    })

    this.structures = []
    this.structureService.getAll(1,this.user.idEntite).subscribe((res:any)=>{
      this.structures = res
    })

    this.structureservices = []
    this.structureService.getAllStructureByUser(this.user.id).subscribe((res: any) => {
      this.structureservices = res
    })

    this.institutionService.getAll().subscribe((res: any) => {
      this.institutions = res
    })


  }

  file: string | Blob =""
  onFileChange(event:any) {
    if (event.target.files.length > 0) {
      this.file = event.target.files[0];
    }
  }

  saveAffectation(value) {
    let val = {
      idRequete: this.selected_data.id,
      idStructure: value.idStructure,
      listeemails: this.structureservices.find(e => (e.id == value.idStructure)).contact,
      typeStructure: 'Service',
      idEntite:this.user.idEntite,
      idEtape: 2,
    }
    // if (this.selected_data.affectation.length != 1) {
    //   AlertNotif.finish("Erreur", "Cette requête a été déjà affectée.", 'error');
    //   return;
    // }

    if (this.selected_data.reponse.length > 0) {
      AlertNotif.finish("Erreur", "Vous ne pouvez plus affecté cette requete car une réponse a été déjà proposée.", 'error');
      return;
    }

    this.requeteService.createAffectation(val).subscribe((res: any) => {
      this.init(this.page)
      this.modalService.dismissAll()
      AlertNotif.finish("Nouvelle affectation", "Affectation effectué avec succès", 'success')
    })
  }
  saveTransmitReponse(value) {

  }
  saveReponse(value) {
    if(value.texteReponseApportee == null || value.texteReponseApportee == ""){
      AlertNotif.finish("Erreur", "Veuillez saisir votre réponse", 'error');
      return;
    }
    let complementReponse = "";
    if (value.interrompu == true)
      complementReponse = "\n\nRaison de l'interruption: " + "\n" + value.raisonRejet;
    else
      if (value.rejet == true)
        complementReponse = "\n\nRaison du rejet: " + "\n" + value.raisonRejet;

    if (value.interrompu == true)
      if (value.texteReponseApportee.indexOf("Raison de l'interruption:") == -1)
        value.texteReponseApportee += complementReponse;

    if (value.rejet == true)
      if (value.texteReponseApportee.indexOf("Raison du rejet:") == -1)
        value.texteReponseApportee += complementReponse;

    // var val = {
    //   idRequete: this.selected_data.id,
    //   typeStructure: 'Direction',
    //   texteReponse: value.texteReponseApportee,
    //   interrompu: value.interrompu,
    //   idEntite:this.user.idEntite,
    //   rejete: value.rejete,
    //   raisonRejet: value.raisonRejet
      
    // };
    let formData = new FormData()
    formData.append('idRequete', this.selected_data.id)
    formData.append('typeStructure', 'Direction')
    formData.append('texteReponse', value.texteReponseApportee)
    formData.append('interrompu', value.interrompu)
    formData.append('idEntite', this.user.idEntite)
    formData.append('rejete', value.rejete)
    formData.append('raisonRejet', value.raisonRejet)
    formData.append('fichier', this.file)
    /*if (this.selected_data.reponse.length > 0) {
      this.selected_data.reponse.forEach(item => {
        if (item.typeStructure == 'Direction')
          this.selected_data.texteReponseApportee = item.texteReponse;
        if (item.typeStructure == 'Service')
          this.selected_data.reponseService = item.texteReponse;
      });
    }*/

    this.requeteService.saveReponse(formData).subscribe((res: any) => {
      if (this.isSended) {
        var paramInternal = {
          idRequete: this.selected_data.id,
          typeStructure: 'Direction',
          idEntite:this.user.idEntite,
          typeSuperieur: 'Usager',
          idEtape: 6,
        };
        this.requeteService.transmettreReponse(paramInternal).subscribe((rest: any) => {
          this.init(this.page)
          this.modalService.dismissAll()
          this.file=""
          AlertNotif.finish("Nouvelle réponse", "Réponse envoyée et transmise avec succès", 'success')
          setTimeout(()=>{                          
            window.location.reload()
        }, 2000);
          
        })
      } else {
        this.init(this.page)
        this.modalService.dismissAll()
        this.file=""
        AlertNotif.finish("Nouvelle réponse", "Réponse envoyée avec succès", 'success')
        setTimeout(()=>{                          
          window.location.reload()
      }, 2000);
      }
    })
    
  }
  Archiver_Requete(value) {

    if(value.texteArchive == null || value.texteArchive.trim() == ""){
      AlertNotif.finish("Erreur", "Veuillez saisir votre motif", 'error');
      return;
    }

    var val = {
      idRequete: this.selected_data.id,
      texteReponse: value.texteArchive,
      idEntite:this.user.idEntite
    };
    this.requeteService.archiverReque(val).subscribe((res: any) => {

        this.init(this.page)
        this.modalService.dismissAll()
        AlertNotif.finish("Archive", "Préoccupation archivée avec succès", 'success')
        setTimeout(()=>{                          
          window.location.reload()
      }, 2000);
    })
    
  }


  transmettreReponseRapide(value) {
    var param = {
      codeRequete: this.selected_data.codeRequete,
      emailusager: this.selected_data.usager ==null ? this.selected_data.email : this.selected_data.usager.email,
      emailstructure: this.user.email,
      idEntite:this.user.idEntite,
      message: value.message,
      nomprenomsusager: this.selected_data.usager ==null ? this.selected_data.identity : this.selected_data.usager.nom,
      type:value.type,
      
    };
    this.requeteService.mailUsager(param).subscribe((rest: any) => {
      this.init(this.page)
      this.modalService.dismissAll()
      if(rest.status == "error"){
        AlertNotif.finish("Erreur", rest.message, 'error')
      }else{
        AlertNotif.finish("Mail Usager", "Mail envoyé avec succès au "+rest.message, 'success')
      }
    })
  }
  mailStructure(value) {
    var param = {
      codeRequete: this.selected_data.codeRequete,
      receiverId: value.receiverId,
      emailstructure: this.user.email,
      idEntite:this.user.idEntite,
      message: value.message,
      type:value.type,
      
    };
    this.requeteService.mailStructure(param).subscribe((rest: any) => {
      this.init(this.page)
      this.modalService.dismissAll()

      if(rest.status == "error"){
        AlertNotif.finish("Erreur", rest.message, 'error')
      }else{
        AlertNotif.finish("Mail Structure", "Mail envoyé avec succès au "+rest.message, 'success')
      }
    })
  }

  transmettreReponse() {
    if (this.selected_data == null) {
      AlertNotif.finish("Erreur", "Veuillez selectionnez un élément puis réessayer", 'error');
      return;
    }

    if (this.selected_data.finalise == 1) {
      AlertNotif.finish("Erreur", "Réponse déjà transmise à l'usager.", 'error');
      return;
    }


    if (this.selected_data.reponse.length > 0) {
      let check = this.selected_data.reponse.filter((item) => (item.typeStructure=='Direction'));
      if(check.length == 0){
        AlertNotif.finish("Erreur","Veuillez affecter ou traiter la préoccupation." , 'error') ;
      }
    }else{
      AlertNotif.finish("Erreur", "Veuillez affecter ou traiter la préoccupation.", 'error');
    }


    if (this.selected_data.reponseStructure == '' || this.selected_data.reponseStructure == null) {
      AlertNotif.finish("Erreur", "Veuillez valider la réponse de votre structure avant de transmettre.", 'error');
      return;
    }


    var msgConfirm = "Voulez-vous transmettre la réponse ?";
    var confirmResult = confirm(msgConfirm);
    if (confirmResult === false) return;
    var param = {
      idRequete: this.selected_data.id,
      typeStructure: 'Direction',
      typeSuperieur: 'Usager',
      idEntite:this.user.idEntite,
      idEtape: 6,
    };
    this.requeteService.transmettreReponse(param).subscribe((rest: any) => {
      this.init(this.page)
      this.modalService.dismissAll()
      AlertNotif.finish("Nouvelle réponse", "Réponse envoyée et transmise avec succès", 'success')
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


  __services=[]
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
      id_user: this.user.id,
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
  transfertInternePreocuppation(value){
      

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
      idEntite: this.user.idEntite,
      id_user: this.user.id,
      idPrestation: value.idPrestation,
    };
    if (param.idStructure == null || param.idStructure == "") {
      AlertNotif.finish("Erreur", "Veuillez sélectionner la structure", 'error');
      return;
    }else if (param.idPrestation == null || param.idPrestation == "") {
      AlertNotif.finish("Erreur", "Veuillez sélectionner la prestation", 'error');
      return;
    }
    AlertNotif.finishConfirm("Transférer cette préoccupation à la structure",
    "Cette action est irreversible. Voulez-vous continuer ?").then((result) => {
      if (result.value) {
        this.requeteService.transfertRequetInterne(param,this.selected_data.id).subscribe((rest: any) => {
          this.init(this.page)
          this.modalService.dismissAll()
          AlertNotif.finish("Transfert préoccupation", "Transfert effectué avec succès", 'success')
        })
      }
    })
  }
  reorienterPreocuppation(value){

    if (this.selected_data == null) {
      AlertNotif.finish("Erreur", "Veuillez selectionnez un élément puis réessayer ", 'error');
      return;
    }

    if (this.selected_data.finalise == 1) {
      AlertNotif.finish("Erreur", "Réponse déjà transmise à l'usager.", 'error');
      return;
    }
    let idStructure=0
    if(value.idPrestation=="440"){
      idStructure=58
    }
    if(value.idPrestation=="441"){
      idStructure=75
    }
    var param = {
      idStructure: idStructure,
      idEntite: this.user.idEntite,
      idPrestation: value.idPrestation,
    };
    AlertNotif.finishConfirm("Réorienter cette préoccupation",
    "Cette action est irreversible. Voulez-vous continuer ?").then((result) => {
      if (result.value) {
        this.requeteService.transfertRequetInterne(param,this.selected_data.id).subscribe((rest: any) => {
          this.init(this.page)
          this.modalService.dismissAll()
          AlertNotif.finish("Réorientation préoccupation", "Réorientation effectué avec succès", 'success')
        })
      }
    })
  }
}
