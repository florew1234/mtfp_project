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
  selector: 'app-list-requete-update',
  templateUrl: './list-requete-update.component.html',
  styleUrls: ['./list-requete-update.component.css']
})
export class ListRequeteUpdateComponent implements OnInit {

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

  search() {
    this.data = []
    this._temp = []
    this.requeteService.getAllRequest(this.user.idEntite,this.searchText, 0, this.user.id, "",
      "", this.page).subscribe((res: any) => {
        this.spinner.hide();
        this.data = res.data;
        // this.data = res.data.filter(e=>{
        //   if(e.lastparcours != null){
        //     return (e.lastparcours.idEtape==1) || 
        //               (e.lastparcours.idEtape==5) || 
        //               (e.lastparcours.idEtape==7 && e.lastparcours.idStructure == this.user.agent_user.idStructure) ||
        //               (e.lastparcours.idEtape==8 && e.lastparcours.idEntite == this.user.idEntite);
        //   }else{
        //     return (e.lastparcours == null);
        //   }
        // })
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

    // this.RelanceAWho = ""
    // this.ValStruRelance = ""
    // this.cpt = 0
    // if (this.selected_data.affectation.length > 0) {
    //   this.selected_data.affectation.forEach(item => {
    //     this.cpt++;
    //     // console.log("Cpt : "+this.cpt,"Nombre : "+this.selected_data.affectation.length,"itemStruc : "+item.idStructure,"UserStructure : "+this.user.agent_user.idStructure)
    //     if (this.cpt == this.selected_data.affectation.length && item.idStructure != this.user.agent_user.idStructure){
    //          this.RelanceAWho = item.typeStructure;
    //          this.ValStruRelance = item.idStructure;
    //       }
    //   })
    // }
    // this.cpt = 0
    // if (this.selected_data.parcours.length > 0) {
    //   this.selected_data.parcours.forEach(item => {
    //     this.cpt++;
    //     if (this.cpt == this.selected_data.parcours.length && item.idStructure == this.user.agent_user.idStructure){
    //       this.RelanceAWho = ""
    //       this.ValStruRelance = ""
    //       }
    //   })
    // }
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
    console.log('eeeeeeeeeeeeeeeeeee')
    console.log(this.user)
    this._temp = []
    this.data = []
    this.requeteService.getAllRequest(this.user.idEntite,null, 0, this.user.id, "", "", page).subscribe((res: any) => {
        this.spinner.hide();
        this.subject.next(res);
        this.data = res.data;
        // this.data = res.data.filter(e=>{
        //   if(e.lastparcours != null){
        //     return (e.lastparcours.idEtape==1) || 
        //               (e.lastparcours.idEtape==5) || 
        //               (e.lastparcours.idEtape==7 && e.lastparcours.idStructure == this.user.agent_user.idStructure) ||
        //               (e.lastparcours.idEtape==8 && e.lastparcours.idEntite == this.user.idEntite);
        //   }else{
        //     return (e.lastparcours == null);
        //   }
        // })
        this._temp = this.data
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

  Modifier_Requete(value) {

    if(value.plainte == null ){
      AlertNotif.finish("Erreur", "Veuillez sélectionner le type", 'error');
      return;
    }

    var val = {
      idRequete: this.selected_data.id,
      plainte: value.plainte,
    };
    this.requeteService.ModifierReque(val).subscribe((res: any) => {

        this.init(this.page)
        this.modalService.dismissAll()
        AlertNotif.finish("Modification", "Modification effectuée avec succès", 'success')
        setTimeout(()=>{                          
          window.location.reload()
      }, 2000);
    })
    
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
