import { Component, OnInit, Input } from '@angular/core';
import { PipeTransform } from '@angular/core';
import { DecimalPipe } from '@angular/common';
import { FormControl } from '@angular/forms';

import { Observable } from 'rxjs';
import { map, startWith } from 'rxjs/operators';
import { NgbModal, ModalDismissReasons } from '@ng-bootstrap/ng-bootstrap';
import { Router, ActivatedRoute } from '@angular/router';
import { UserService } from '../../../core/_services/user.service';

import { NgxSpinnerService } from 'ngx-spinner';
import { AlertNotif } from '../../../alert';
import { TranslateService } from '@ngx-translate/core';
import { User } from '../../../core/_models/user.model';
import { Roles } from '../../../core/_models/roles';
import { StructureService } from '../../../core/_services/structure.service';
import { ActeurService } from '../../../core/_services/acteur.service';
import { Acteur } from '../../../core/_models/acteur.model';
import { ServiceService } from '../../../core/_services/service.service';
import { TypeService } from '../../../core/_services/type.service';
import { LocalService } from '../../../core/_services/browser-storages/local.service';

@Component({
  selector: 'app-all-services',
  templateUrl: './all-services.component.html',
  styleUrls: ['./all-services.component.css']
})
export class AllServicesComponent implements OnInit {

  activeTab = 1;
  activeTab2=1;
  
  @Input() cssClasses = '';
  errormessage = ""
  page = 1;
  pageSize = 10;
  searchText = ""
  closeResult = '';
  errorajout = ""
  permissions: any[]
  def_cost=0
  error = ""
  data: any[] = [];
  _temp: any[] = [];
  showNbreJour = false
  show_access_online=false
  selected = [
  ];
  current_permissions: any[] = []
  collectionSize = 0;
  selected_data: any
  default_data:any={
    access_online: 0,
    access_url: null,
    view_url: null,
    consiste: "",
    contactPresidentSG: null,
    cout: 0,
    dateredac: "",
    delai: "",
    delaiFixe: 0,
    echeance: "",
    hide_for_public: 0,
    idEntite: null,
    idParent: null,
    idType: null,
    interetDemanderTot: "",
    interetDemandeur: "",
    libelle: "",
    lieuDepot: "",
    lieuRetrait: "",
    listepieces: [],
    nbreJours: 0,
    nomPresidentSG: null,
    nomSousG: null,
    obligatoire: "",
    piecesAFournir: null,
    published: 0,
    published_at: null,
    published_by: null,
    submited: 0,
    textesRegissantPrestation: ""
  }

  search() {
    this.data = this._temp.filter(r => {
      const term = this.searchText.toLowerCase();
      return r.libelle.toLowerCase().includes(term)
    })
    this.collectionSize = this.data.length
  }

  openAddModal(content) {
    this.modalService.open(content, { ariaLabelledBy: 'modal-basic-title' }).result.then((result) => {
      this.closeResult = `Closed with: ${result}`;
    }, (reason) => {
      this.closeResult = `Dismissed ${this.getDismissReason(reason)}`;
    });
  }

  openEditModal(content) {
    if (this.selected_data == null) {
      AlertNotif.finish("Erreur", "Veuillez selectionnez un élément puis réessayer", 'error');
      return;
    }
    if(this.selected_data.id==440 || this.selected_data.id==441){
      AlertNotif.finish("Modification", "Impossible de modifié cet élément", 'error')
      return
    }
    this.listepieces = []
    for (var i = 0; i < this.selected_data.listepieces.length; i++) {
      var piece = {
        id: this.listepieces.length + 1,
        libellePiece: this.selected_data.listepieces[i].libellePiece,
      };
      this.listepieces.push(piece);
    }
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

  types = []
  listepieces = []

  constructor(
    private modalService: NgbModal,
    private userService: UserService,
    private router: Router,
    private prestationService: ServiceService,
    private structureService: StructureService,
    private typesService: TypeService,
    private translate: TranslateService,
    private spinner: NgxSpinnerService,
    private activatedRoute: ActivatedRoute,
    private localStorageService:LocalService
  ) { }

  structures: [] = []

  
  user:any
  ngOnInit() {
    if (localStorage.getItem('mataccueilUserData') != null) {
      this.user = this.localStorageService.getJsonValue("mataccueilUserData")
      this.init()
    }
  }
  checked(event, el) {
    this.selected_data = el
  }
  
  init() {
    this._temp = []
    this.data = []
    if(this.user.agent_user!=null && (this.user.profil_user.direction==1)){
      this.default_data.idParent=this.user.agent_user.idStructure
      this.prestationService.getAllByStructure(this.user.agent_user.idStructure).subscribe((res: any) => {
        this.spinner.hide();
        this.data = res
        this._temp = this.data
        this.collectionSize = this.data.length
      })
    }else if(this.user.agent_user!=null && this.user.profil_user.pointfocal==1){
      this.default_data.idParent=this.user.agent_user.structure.idParent
      this.prestationService.getAllByStructure(this.user.agent_user.structure.idParent).subscribe((res: any) => {
        this.spinner.hide();
        this.data = res.filter(e=>((e.submited==1 || e.submited==true)))
        this._temp = this.data
        this.collectionSize = this.data.length
      })
    }else if(this.user.agent_user!=null && this.user.profil_user.saisie_adjoint==1){
      this.prestationService.getAllByCreator().subscribe((res: any) => {
        this.spinner.hide();
        this.data = res
        this._temp = this.data
        this.collectionSize = this.data.length
      })
    }else{
      this.prestationService.getAll(this.user.idEntite).subscribe((res: any) => {
        this.spinner.hide();
        this.data = res
        this._temp = this.data
        this.collectionSize = this.data.length
      })
    }
   
    this.structures = []
    this.structureService.getAll(0  ,this.user.idEntite).subscribe((res: any) => {
      this.spinner.hide();
      this.structures = res
    })
    this.types = []
    this.typesService.getAll(this.user.idEntite).subscribe((res: any) => {
      this.spinner.hide();
      this.types = res
    })
  }

  create(value) {
    var getNbreJours=0;
    if(this.default_data.delaiFixe==true){getNbreJours=this.default_data.nbreJours;}
    this.default_data.nbreJours=getNbreJours
    var geturl=null
    if(this.default_data.access_online==true){geturl=this.default_data.access_url;}
    this.default_data.access_url=geturl,
    this.default_data.cout=this.default_data.cout=="" ? 0 : +this.default_data.cout  
    this.default_data.cout=this.default_data.cout=="" ? 0 : +this.default_data.cout  
    this.default_data.idEntite=this.user.idEntite
    this.prestationService.create(this.default_data).subscribe((res: any) => {

      this.modalService.dismissAll()
      this.init()

      //this.translate.instant('HOME.TITLE')
      AlertNotif.finish("Nouvel ajout", "Ajout effectué avec succès", 'success')
    }, (err) => {

      if (err.error.detail != null) {
        AlertNotif.finish("Nouvel ajout", err.error.detail, 'error')
      } else {
        AlertNotif.finish("Nouvel ajout", "Erreur, Verifiez que vous avez une bonne connexion internet", 'error')
      }
    })
  }
  filter(event){
    let state=event.target.value
    this.data = []
    if(state=="all"){
      this.data=this._temp
    }else{
      this.data=this._temp.filter(e=>(e.published==+state))
    }
    this.collectionSize = this.data.length
  }

  archive() {
    AlertNotif.finishConfirm("Suppression",
      "Cette action est irreversible. Voulez-vous continuer ?").then((result) => {
        if (result.value) {
          this.prestationService.delete(this.selected_data.id).subscribe((res: any) => {
            this.init()
            AlertNotif.finish("Suppression", "Suppression effectuée avec succès", 'success')
           
          }, (err) => {
            AlertNotif.finish("Suppression", "Erreur, Verifiez que vous avez une bonne connexion internet", 'error')
          })
        }
      })
  }
  submit() {
    if (this.selected_data == null) {
      AlertNotif.finish("Erreur", "Veuillez selectionnez un élément puis réessayer", 'error');
      return;
    }
    if(this.selected_data.submited==1 || this.selected_data.submited==true){
      AlertNotif.finish("Soumettre cette prestation", "Erreur, Cette prestation a déjà été publiée", 'error')
    }
    this.selected_data.submited=true
    this.selected_data.published=false
    AlertNotif.finishConfirm("Soummettre cette prestation",
      "Cette action est irreversible. Voulez-vous continuer ?").then((result) => {
        if (result.value) {
          this.prestationService.update(this.selected_data,this.selected_data.id).subscribe((res: any) => {
            AlertNotif.finish("Soumettre cette prestation", "Soumission effectuée avec succès", 'success')
            this.init()
          }, (err) => {
            AlertNotif.finish("Soumettre cette prestation", "Erreur, Verifiez que vous avez une bonne connexion internet", 'error')
          })
        }
      })
  }
  publish() {
    if (this.selected_data == null) {
      AlertNotif.finish("Erreur", "Veuillez selectionnez un élément puis réessayer", 'error');
      return;
    }
    if(this.selected_data.published==1 || this.selected_data.published==true){
      AlertNotif.finish("Publier cette prestation", "Erreur, Cette prestation a déjà été publiée", 'error')
    }
    this.selected_data.published=true
    this.selected_data.submited=true
    AlertNotif.finishConfirm("Publier cette prestation",
      "Cette action est irreversible. Voulez-vous continuer ?").then((result) => {
        if (result.value) {
          this.prestationService.update(this.selected_data,this.selected_data.id).subscribe((res: any) => {
            AlertNotif.finish("Publier cette prestation", "Publication effectuée avec succès", 'success')
            this.init()
          }, (err) => {
            AlertNotif.finish("Publier cette prestation", "Erreur, Verifiez que vous avez une bonne connexion internet", 'error')
          })
        }
      })
  }
  edit(value) {
   // value.id = this.selected_data.id
    var getNbreJours=0;
    var geturl=null;
    if(this.selected_data.delaiFixe==true){getNbreJours=this.selected_data.nbreJours;}

    if(this.selected_data.access_online==true){geturl=this.selected_data.access_url;}
    this.selected_data.access_url=geturl,
    this.selected_data.nbreJours=getNbreJours,
   //   value.published=this.selected_data.published
    this.selected_data.idEntite=this.user.idEntite
        this.prestationService.update(this.selected_data, this.selected_data.id).subscribe((res) => {
          this.modalService.dismissAll()
          this.init()
          AlertNotif.finish("Nouvelle modification", "Motification effectué avec succès", 'success')
        }, (err) => {
      AlertNotif.finish("Nouvelle modification", "Erreur, Verifiez que vous avez une bonne connexion internet", 'error')
    })
  }

  savePiece() {
  
    var param = {
      id: this.selected_data.id,
      libelle: this.selected_data.libelle,
      listepieces: this.listepieces,
      idEntite:this.user.idEntite
    };

    this.prestationService.savePiece(param).subscribe((res: any) => {
      this.modalService.dismissAll()
      AlertNotif.finish("Mise à jour pieces", "Mise à jour effectué avec succès", 'success')
      this.init()
    }, (err) => {

      if (err.error.detail != null) {
        AlertNotif.finish("Mise à jour pieces", err.error.detail, 'error')
      } else {
        AlertNotif.finish("Mise à jour pieces", "Erreur, Verifiez que vous avez une bonne connexion internet", 'error')
      }
    })
  }
  addPiece(value) {
    this.listepieces.forEach(function (item) {
      if (item.libellePiece == value.libellePiece)
        AlertNotif.finish("Ajout piece", "Cette pièce a été déjà ajoutée.", 'error')
      return;
    });
    console.log(value)
    value.id = this.listepieces.length + 1,
      this.listepieces.push(value);
  }

  removeRow(i) {
    var msgConfirm = "Souhaitez-vous vraiment supprimer la ligne ?";
    var confirmResult = confirm(msgConfirm);
    if (confirmResult === false) return;

    this.listepieces.splice(i, 1)
  }

}
