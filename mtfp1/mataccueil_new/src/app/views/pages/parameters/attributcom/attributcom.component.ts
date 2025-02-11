import { Component, OnInit, Input } from '@angular/core';
import { PipeTransform } from '@angular/core';
import { DecimalPipe } from '@angular/common';
import { FormControl } from '@angular/forms';

import { Observable } from 'rxjs';
import { map, startWith } from 'rxjs/operators';
import { NgbModal, ModalDismissReasons } from '@ng-bootstrap/ng-bootstrap';
import { Router, ActivatedRoute } from '@angular/router';
import { UserService } from '../../../../core/_services/user.service';

import { NgxSpinnerService } from 'ngx-spinner';
import { AlertNotif } from '../../../../alert';
import { TranslateService } from '@ngx-translate/core';
import { User } from '../../../../core/_models/user.model';
import { Roles } from '../../../../core/_models/roles';
import { StructureService } from '../../../../core/_services/structure.service';
import { ActeurService } from '../../../../core/_services/acteur.service';
import { Acteur } from '../../../../core/_models/acteur.model';
import { ServiceService } from '../../../../core/_services/service.service';
import { TypeService } from '../../../../core/_services/type.service';
import { LocalService } from '../../../../core/_services/browser-storages/local.service';


@Component({
  selector: 'app-attributcom',
  templateUrl: './attributcom.component.html',
  styleUrls: ['./attributcom.component.css']
})
export class AttributcomComponent implements OnInit {

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
  listComm = []
  listUsers = []
  data: any[] = [];
  _temp: any[] = [];
  showNbreJour = false
  show_access_online=false
  selected = [
  ];
  current_permissions: any[] = []
  collectionSize = 0;
  selected_data: any
  selected_iduser: any
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
    submited: 0,
    textesRegissantPrestation: ""
  }

  search() {
    
    this.data = []
    this.prestationService.getAllAttrib(this.selected_iduser).subscribe((res: any) => {
      this.spinner.hide();
      this.data = res
      this._temp = this.data
      this.collectionSize = this.data.length
    })
  }

  openAddModal(content) {
    if(this.selected_iduser == "" || this.selected_iduser == null){
      AlertNotif.finish("Erreur", "Veuillez selectionnez un acteur", 'error');
      return;
    }
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
  departement:[]=[]

  constructor(
    private modalService: NgbModal,
    private userService: UserService,
    private router: Router,
    private acteursService:ActeurService,
    private prestationService: ServiceService,
    private structureService: StructureService,
    private typesService: TypeService,
    private translate: TranslateService,
    private spinner: NgxSpinnerService,
    private activatedRoute: ActivatedRoute,
    private localStorageService:LocalService
  ) { }

  structures: [] = []
  selectedDepart = null
  commune = []
  idDepa:any

  user:any
  ngOnInit() {
    if (localStorage.getItem('mataccueilUserData') != null) {
      this.user = this.localStorageService.getJsonValue("mataccueilUserData")
      this.init()
    }
  }
  checked(event, el) {
    
    this.selected_data = el
    this.idDepa = el.commune.depart_id
    this.chargerCommune(this.idDepa)
  }
  onDepartChange(event){
    this.selectedDepart=+event.target.value
    this.chargerCommune(this.selectedDepart)
  }
  chargerCommune(idDepartt){
    this.commune = []
    this.acteursService.getAllCommune(idDepartt).subscribe((res: any) => {
      this.commune = res
    })
  }
  init() {
    
    this._temp = []
    this.data = []
      this.prestationService.getAllAttrib(this.user.id).subscribe((res: any) => {
        this.spinner.hide();
        this.data = res
        this._temp = this.data
        this.collectionSize = this.data.length
      })
      this.listUsers = []
      this.userService.getAllActeur(this.user.idEntite).subscribe((res:any)=>{
        this.listUsers = res
      })
      this.departement=[]
        this.acteursService.getAllDepart().subscribe((res:any)=>{
          this.departement=res
      })
  }

  create(value) {
    
    if(value.idDepart == null || value.idDepart == ""){
      AlertNotif.finish("Erreur","Veuillez choisir un département.", 'error')
    }else if(value.idComm == null || value.idComm == ""){
      AlertNotif.finish("Erreur","Veuillez choisir une commune.", 'error')
    }else{
      value.id_user = this.selected_iduser
      this.acteursService.createAttri(value).subscribe((res:any)=>{
        if(res.success == false){
          AlertNotif.finish("Erreur","Cette commune est déjà ajouté à cet acteur", 'error')
        }else{
          AlertNotif.finish("Nouvel ajout","Ajout effectué avec succès" , 'success')
        }
       this.selected_data = null
       this.modalService.dismissAll() 
       this.search() 
      },(err)=>{
        if(err.error.detail!=null){    
          AlertNotif.finish("Nouvel ajout", err.error.detail, 'error')
        }else{
          AlertNotif.finish("Nouvel ajout", "Erreur, Verifiez que vous avez une bonne connexion internet", 'error')
        }
      })
    }
  }


  archive() {
    if(this.selected_data.id==440 || this.selected_data.id==441){
      AlertNotif.finish("Suppression", "Impossible de supprimé cet élément", 'error')
      return
    }
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

  edit(value) {
    
    if(value.idDepart == null || value.idDepart == ""){
      AlertNotif.finish("Erreur","Veuillez choisir un département.", 'error')
    }else if(value.idComm == null || value.idComm == ""){
      AlertNotif.finish("Erreur","Veuillez choisir une commune.", 'error')
    }else{
      value.id= this.selected_data.id
      value.id_user= this.selected_data.id_user
      this.acteursService.updateAttri(value,this.selected_data.id).subscribe((res)=>{
        if(res.success == false){
          AlertNotif.finish("Erreur","Cette commune est déjà ajouté à cet acteur", 'error')
        }else{
          AlertNotif.finish("Nouvelle modification",  "Motification effectué avec succès", 'success')
        }
        this.selected_data = null
        this.modalService.dismissAll()
        this.search()
      }, (err)=>{
        AlertNotif.finish("Nouvelle modification", "Erreur, Verifiez que vous avez une bonne connexion internet", 'error')
      })
    }
  }

  removeRow(i) {
    var msgConfirm = "Souhaitez-vous vraiment supprimer la ligne ?";
    var confirmResult = confirm(msgConfirm);
    if (confirmResult === false) return;

    this.listepieces.splice(i, 1)
  }
}
