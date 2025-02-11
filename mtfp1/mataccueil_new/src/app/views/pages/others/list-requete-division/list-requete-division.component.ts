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
import { LocalService } from '../../../../core/_services/browser-storages/local.service';
import { ServiceService } from '../../../../core/_services/service.service';
import { StructureService } from '../../../../core/_services/structure.service';
import { UsagerService } from '../../../../core/_services/usager.service';
import { Config } from '../../../../app.config';


@Component({
  selector: 'app-list-requete-division',
  templateUrl: './list-requete-division.component.html',
  styleUrls: ['./list-requete-division.component.css']
})
export class ListRequeteDivisionComponent implements OnInit {

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

  search() {
    this.data = []
    this._temp = []
    this.requeteService.getAllRequest(this.user.idEntite,this.searchText, 0, this.user.id, "Division",
      this.checkType().id, this.page).subscribe((res: any) => {
        this.spinner.hide();
        // this.data = res.data
        this.data = res.data.filter(e=>{
          if(e.lastparcours != null){
            return (e.lastparcours.idEtape==1) || 
                    (e.lastparcours.idEtape==3 && e.lastparcours.idStructure == this.user.agent_user.idStructure);
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
    private router: Router,
    private requeteService: RequeteService,
    private localService: LocalService,
    private prestationService: ServiceService,
    private structureService: StructureService,
    private usagersService: UsagerService,
    private translate: TranslateService,
    private spinner: NgxSpinnerService,
    private activatedRoute: ActivatedRoute,
    private etapeService: EtapeService
  ) { }

  etapes = []
  services = []
  departements = []
  structureservices = []
  user: any
  compteData = 0
  isGeneralDirector = false
  isSended = false
  typeRequete = "requetes"
  hide_reponse_form_action=false
  usager_full_name=""
  
  checked(event, el) {
    this.selected_data = el
    if(this.selected_data.usager == null){
      this.usager_full_name=" (PFC) "+this.selected_data.email+" Contact : "+this.selected_data.contact
    }else{
      this.usager_full_name=this.selected_data.usager.nom+" "+this.selected_data.usager.prenoms
    }
    if (this.selected_data.reponse.length > 0) {
      this.selected_data.reponse.forEach(item => {
          if (item.typeStructure == 'Division')
          {
            this.selected_data.texteReponseApportee = item.texteReponse;
            this.hide_reponse_form_action=false
            if(item.siTransmis==1){
              this.hide_reponse_form_action=true
            }
          }
        /*if (item.typeStructure == 'Service')
          this.selected_data.reponseService = item.texteReponse;*/
      });
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

  init(page) {
    this._temp = []
    this.data = []
    this.requeteService.getAllRequest(this.user.idEntite,null, 0, this.user.id, this.user.agent_user.idStructure,this.checkType().id,page).subscribe((res: any) => {
        this.spinner.hide();
        this.subject.next(res);
        // this.data = res.data
        this.data = res.data.filter(e=>{
          if(e.lastparcours != null){
            return (e.lastparcours.idEtape==1) || 
                    (e.lastparcours.idEtape==3 && e.lastparcours.idStructure == this.user.agent_user.idStructure);
          }else{
            return (e.lastparcours == null);
          }
        })
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
    })

    this.structureservices = []
    this.structureService.getAllStructureByUser(this.user.id).subscribe((res: any) => {
      this.structureservices = res
    })

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

    var val = {
      idRequete: this.selected_data.id,
      typeStructure: 'Division',
      texteReponse: value.texteReponseApportee,
      interrompu: value.interrompu,
      rejete: value.rejete,
      idEntite:this.user.idEntite,
      raisonRejet: value.raisonRejet
    };
    this.requeteService.saveReponse(val).subscribe((res: any) => {
      if (this.isSended) {
        var paramInternal = {
          idRequete: this.selected_data.id,
          typeStructure: 'Division',
          typeSuperieur: 'Service',
          idEntite:this.user.idEntite,
          idEtape: 4,
        };
        this.requeteService.transmettreReponse(paramInternal).subscribe((rest: any) => {
          this.init(this.page)
          this.modalService.dismissAll()
          AlertNotif.finish("Nouvelle réponse", "Réponse envoyée et transmise avec succès", 'success')
        })
      } else {
        this.init(this.page)
        this.modalService.dismissAll()
        AlertNotif.finish("Nouvelle réponse", "Réponse envoyée avec succès", 'success')
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
      let check = this.selected_data.reponse.filter((item) => (item.typeStructure=='Division'));
      if(check.length == 0){
        AlertNotif.finish("Erreur","Veuillez affecter ou traiter la préoccupation." , 'error') ;
      }
    }else{
     AlertNotif.finish("Erreur", "Veuillez affecter ou traiter la préoccupation.", 'error');
    }


    if (this.selected_data.reponseDivision == '' || this.selected_data.reponseDivision == null) {
      AlertNotif.finish("Erreur", "Veuillez valider la réponse de votre structure avant de transmettre.", 'error');
      return;
    }
    if (this.selected_data.reponse.length > 0) {
      let check = this.selected_data.reponse.filter((item) => (item.typeStructure=='Division' && item.siTransmis==1));
      if(check.length != 0){
        AlertNotif.finish("Erreur","Votre réponse à déjà été transmise à votre supérieur" , 'error') ;
      }
    }

    var msgConfirm = "Voulez-vous transmettre la réponse ?";
    var confirmResult = confirm(msgConfirm);
    if (confirmResult === false) return;
    var param = {
      idRequete: this.selected_data.id,
      typeStructure: 'Division',
      idEntite:this.user.idEntite,
      typeSuperieur: 'Service',
      idEtape: 4,
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
}
