import { Component, OnInit, Input, ViewChild, TemplateRef } from '@angular/core';
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
import { AuthentificationService } from '../../../../core/_services/authentification.service';
import { RdvService } from '../../../../core/_services/rdv.service';
import { RdvCreneauService } from '../../../../core/_services/rdv-creneau.service';
import { DateRdvService } from '../../../../core/_services/date-rdv.service';
import { InstitutionService } from '../../../../core/_services/institution.service';

@Component({
  selector: 'app-espaceusager',
  templateUrl: './espaceusager.component.html',
  styleUrls: ['./espaceusager.component.css']
})
export class EspaceusagerComponent implements OnInit {
  @ViewChild('note') note : TemplateRef<any> | undefined
  @Input() cssClasses = '';
  errormessage = ""
  erroraffectation = ""

  searchText = ""
  closeResult = '';
  permissions: any[] 
  error = ""
  data: any[] = [];
  dataNT: any[] = [];
  _temp: any[] = [];
  collectionSize = 0;
  page = 1;
  pageSize = 10;

  selected = [];
  descrCarr = [];
  current_permissions: any[] = []
  selected_data: any 
  selected_data_note: any
  isSended = false
  notes = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10]
  daterdvs = []
  rdvcreneaus = []
  visible = 0
  selected_service: any
  link_to_prestation=1
  selected_type_preoccupation=0
  structures=[]
  entities=[]
  selectedIdEntite=null
  NULL=null
  loading=false
  entite_vis = false
  mat_aff = false
  idEntite:any
  canSentNew=false
  
  search() {
    this.data = this._temp.filter(r => {
      const term = this.searchText.toLowerCase();
      return r.objet.toLowerCase().includes(term) ||
      r.msgrequest.toLowerCase().includes(term)
    })
    this.collectionSize = this.data.length
  }

  openAddModal(content) {
    this.loading=false
    this.modalService.open(content, { ariaLabelledBy: 'modal-basic-title', size: "lg" }).result.then((result) => {
      this.closeResult = `Closed with: ${result}`;
    }, (reason) => {
      this.closeResult = `Dismissed ${this.getDismissReason(reason)}`;
    });
  }



  openEditModal(content) {
    this.loading=false
    if (this.selected_data != null) {
      this.prepare(this.selectedEntie)
      this.modalService.open(content, { ariaLabelledBy: 'modal-basic-title', size: "lg" }).result.then((result) => {
        this.closeResult = `Closed with: ${result}`;
      }, (reason) => {
        this.closeResult = `Dismissed ${this.getDismissReason(reason)}`;
      });
    } else {
      AlertNotif.finish("Erreur", "Veuillez selectionnez un élément puis réessayer", 'error')
    }
  }
  openEditModal2(content) {
    this.loading=false
    if (this.selected_data2 != null) {
      this.modalService.open(content, { ariaLabelledBy: 'modal-basic-title', size: "lg" }).result.then((result) => {
        this.closeResult = `Closed with: ${result}`;
      }, (reason) => {
        this.closeResult = `Dismissed ${this.getDismissReason(reason)}`;
      });
    } else {
      AlertNotif.finish("Erreur", "Veuillez selectionnez un élément puis réessayer", 'error')
    }
  }
  openNoteModal(content, el) {
    this.selected_data_note = el
    this.loading=false
    this.modalService.open(content, { ariaLabelledBy: 'modal-basic-title', size: "lg" }).result.then((result) => {
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
    private rdvService: RdvService,
    private etapeService: EtapeService,
    private requeteService: RequeteService,
    private localService: LocalService,
    private prestationService: ServiceService,
    private structureService: StructureService,
    private natureService: NatureRequeteService,
    private thematiqueService: TypeService,
    private usagersService: UsagerService,
    private rdvCreneauService: RdvCreneauService,
    private daterdvService: DateRdvService,
    private spinner: NgxSpinnerService,
    private authService: AuthentificationService,
    private activatedRoute: ActivatedRoute,
    private institutionService:InstitutionService
  ) { }

  ChangerFile(file){
    // window.location.href="http://api.mataccueil.sevmtfp.test/api/downloadFile?file="+file
    window.location.href="https://api.mataccueil.gouv.bj/api/downloadFile?file="+file
    // window.location.href="http://localhost:8003/api/downloadFile?file="+file
  }
  etapes = []
  services = []
  g_services = []
  __services = []
  departements = []
  structureservices = []
  themes = []
  natures = []
  institutions = []
  rdvs = []
  detailpiece=[]

  isGeneralDirector = false
  typeRequete = "Préoccupation"

  checkType() {
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

  setVisible() {
    this.visible = 1
  }
  selected_data2=null

  show_actions2=true;
  checked2(event, el){
    this.selected_data2 = el
    this.selected_data2.full_name=this.selected_data2.nom+" "+this.selected_data2.prenoms
    
    if(el.archiver == 1){
      this.canSentNew=true
    }else{
      let check=this.data.filter((el:any)=> el.traiteOuiNon==1 && el.noteUsager==null)
      console.log(check.length)
      if(check.length==0){
        this.canSentNew = true;
      }else{
        this.canSentNew = false
        AlertNotif.finishConfirm("Important", " Nous vous prions de nous laisser votre appréciation sur votre dernière préoccupation satisfait ").then((res:any)=>
        {
          if(res.isConfirmed){
              this.openNoteModal(this.note, el)
          }
        })
      }
      this.canSentNew = check.length==0?true:false;
    }
    if(el.statut==0 && this.canSentNew){
      this.show_actions2=true
    }else{
      this.show_actions2=false
    }
  }

  show_actions=true;
  checked(event, el) {
    this.selected_data = el
    this.mat_aff = false
    console.log(this.selected_data)

    if(el.archiver == 1){
      this.canSentNew=true
    }else{
      let check=this.data.find((el:any)=> el.traiteOuiNon==1 && el.noteUsager==null)
      console.log(check.length)
      if(check==undefined || check==null){
        this.canSentNew = true;
      }else{
        this.canSentNew = false
        AlertNotif.finishConfirm("Important", " Nous vous prions de nous laisser votre appréciation sur la prise en charge de votre dernière préoccupation.").then((res:any)=>
        {
          if(res.isConfirmed){
              this.openNoteModal(this.note, check)
          }
        })
      }
      this.canSentNew = check.length==0?true:false;
    }

    if ( this.canSentNew) {
      if(el.visible==0){
        this.show_actions=true
        if(this.selected_data.service.type.libelle== "Formation" || this.selected_data.service.type.libelle == "Carrière"){
          this.mat_aff = true
        }
      }else{
        this.show_actions=false
      }
    }
  
    this.selectedEntie=el.idEntite
    
    this.thematiqueService.getAll(this.selected_data.idEntite).subscribe((res: any) => {
      this.themes = res
    })

    this.thematiqueService.get(this.selected_data).subscribe((res:any)=>{
      this.descrCarr = res.descr
    })
    this.prestationService.getAllType(this.selected_data.service.idType).subscribe((res:any)=>{
      this.services=res
    })

  }

  show_step(id) {
    return this.etapes.find((e) => (e.id == id))
  }

  url="https://demarchesmtfp.gouv.bj?client_id=26d9d6be-d676-465f-b92c-369b72442c7f&client_secret=f5034b6c80a13d411fa03a8d1f14"
  


  logout() {
    localStorage.removeItem('guvUserToken')
    localStorage.removeItem('guvUserData')
    localStorage.removeItem('mataccueilUserData')
    window.location.href =this.url;
  }

  selectedEntie=null

  ngOnInit(): void {
    
    console.log(localStorage.getItem('mataccueilUserData'))
    
    if (localStorage.getItem('mataccueilUserData') != null) {
      this.user = this.localService.getJsonValue('mataccueilUserData')
      this.user.full_name=this.user.nom+" "+this.user.prenoms
      this.selectedEntie=this.user.institu_id
      //Controle ajouter pour les premiers users qui n'ont pas renseigné leur identite
      if(this.selectedEntie == null || this.selectedEntie == "null"){
        this.selectedEntie = 1 //MTFP par défaut 
      }
      if(this.selectedEntie != null && this.selectedEntie != "" ){
        this.prepare(this.selectedEntie)
      }
      this.institutions=[]
      this.institutionService.getAll().subscribe((res: any) => {
       this.institutions = res
       this.init()
       })
    } 
    
  }
  
  loadRequest() {
    this._temp = []
    this.data = []
    this.dataNT = []
    
    this.requeteService.getAllForUsagerNT(
      this.user.id
      , 1).subscribe((res: any) => {
        this.dataNT = res
      })
    this.requeteService.getAllForUsager(
      this.user.id
      , 1).subscribe((res: any) => {
        this.spinner.hide();
        this.data = res
        this._temp = this.data
        this.collectionSize = this.data.length
      })
      
      this.ChechEtape();
  }
  loadRdv() {
    this.rdvs = []
    this.rdvService.getAllForUsager(this.user.id).subscribe((res: any) => {
      this.rdvs = res
    })
  }
  init() {
    this.loadRequest()
    this.departements = []
    this.usagersService.getAllDepartement().subscribe((res: any) => {
      this.departements = res
    })
    this.prepare(this.user.institu_id)
    this.loadRdv()
  }

  onEntiteChange(event){
    console.log(event.target.value)
    this.selectedEntie=+event.target.value
    this.prepare(this.selectedEntie)

  }

  ChechEtape(){
    this.etapes = []
    this.etapeService.getAll(0).subscribe((res: any) => {
      this.etapes = res
    })
  }
  prepare(idEntite){
    //COntrole ajouter pour les premiers users qui n'ont pas renseigné leur identite
    if(idEntite == null || idEntite == "null"){
      idEntite = 1 //MTFP par défaut 
    }
    this.g_services = []
    this.prestationService.getAll(idEntite).subscribe((res: any) => {
      this.g_services = res
    })
    
      // alert();
      // this.themes=[]
      // this.thematiqueService.getAll().subscribe((res:any)=>{
      //   this.themes=res
      // })
  
    this.structures = []
    this.structureService.getAll(1,idEntite).subscribe((res:any)=>{
      this.structures = res
    })
    this.themes = []
    this.thematiqueService.getAll(idEntite).subscribe((res: any) => {
      this.themes = res
    })
    this.daterdvs = []
    this.daterdvService.getAllActif(idEntite).subscribe((res: any) => {
      this.daterdvs = res
    })

    this.rdvcreneaus = []
    this.rdvCreneauService.getAll(idEntite).subscribe((res: any) => {
      this.rdvcreneaus = res
    })
  }

  addRequeteusager(value) {
    
    let service = null
    if (this.link_to_prestation==1 || this.selected_type_preoccupation==0) {
      service = this.services.filter(e => (e.id == value.idPrestation))[0]
    }else{
      service=this.g_services.filter(e => (e.hide_for_public == 1))[0]
    }
    if(service == null){
      AlertNotif.finish("Erreur","Aucune prestation (Service Usager) par défaut n'est lié à cet entité", 'error')
      return;
    }
      var param = {
        objet: value.objet,
        idPrestation: this.link_to_prestation==0  ? service.id : value.idPrestation,
        nbreJours: service == null ? 0 : service.nbreJours,
        msgrequest: value.msgrequest,
        email: this.user.email,
        idEntite:this.selectedEntie,
        nom: this.user.nom,
        tel: this.user.tel,
        link_to_prestation:this.link_to_prestation,
        idDepartement: this.user.idDepartement,
        interfaceRequete: this.link_to_prestation==1 ? "USAGER"  : "SRU" ,
        idUser: this.user.id,
        plainte: value.plainte,
        matricule: this.mat_aff == true ? value.matricule : '',
        visible: this.visible
      };
      // fichierJoint
      
      if(param.idEntite == null || param.idEntite == ""){
        AlertNotif.finish("Erreur","Veuillez choisir une structure destrinatrice.", 'error')
      }else if(param.plainte == null || param.plainte == "0"){
        AlertNotif.finish("Erreur","Veuillez choisir un type de préoccupation.", 'error')
      }else if(this.mat_aff == true && param.matricule.trim() == ''){
        AlertNotif.finish("Renseigner le matricule", "Champ obligatoire", 'error')
      }else if(param.idPrestation == null || param.idPrestation == ""){
        AlertNotif.finish("Erreur","Veuillez choisir une prestation.", 'error')
      }else if(!param.objet){
        AlertNotif.finish("Renseigner l'objet", "Champ obligatoire", 'error')
      }else if(!param.msgrequest){
        AlertNotif.finish("Renseigner le message", "Champ obligatoire", 'error')
      }else{
        this.loading=true
        this.requeteService.create(param).subscribe((rest: any) => {
          this.init()
          this.visible=0
          this.modalService.dismissAll()
          this.loading=false
          if(rest.status=="error"){
            AlertNotif.finish("Erreur",rest.message, 'error')
          }else{
            if(param.visible==0){
              AlertNotif.finish("Ajout requête", "Requête ajoutée avec succès", 'success')
            }else{
              AlertNotif.finish("Ajout requête", "Requete ajouté et transmis avec succès", 'success')
            }
          }
        })
      }
  }

  openDetailModal(event,content){

    this.detailpiece=[]
    this.prestationService.getServPiece(event.target.value).subscribe((res:any)=>{
      this.detailpiece=res
    })
    
    this.modalService.open(content, {ariaLabelledBy: 'modal-basic-title'}).result.then((result) => {
      this.closeResult = `Closed with: ${result}`;
    }, (reason) => {
      this.closeResult = `Dismissed ${this.getDismissReason(reason)}`;
    });
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
        link_to_prestation: this.selected_data.link_to_prestation,
        idPrestation: this.selected_data.link_to_prestation==0 ? service.id : value.idPrestation,
        nbreJours: service == null ? 0 : service.nbreJours,
        msgrequest: this.selected_data.msgrequest,
        email: value.email,
        idEntite:this.selectedEntie,
        nom: this.selected_data.nom,
        tel: this.selected_data.tel,
        idDepartement: this.selected_data.usager.idDepartement,
        interfaceRequete:  "USAGER" ,
        natureRequete: value.natureRequete,
        idUser: this.selected_data.usager.id,
        matricule: this.mat_aff == true ? value.matricul : '',
        plainte: value.plainte
      };
      // 
      if(param.idEntite == null || param.idEntite == ""){
        AlertNotif.finish("Erreur","Veuillez choisir une structure destrinatrice.", 'error')
      }else if(param.plainte == null || param.plainte == "0"){
        AlertNotif.finish("Erreur","Veuillez choisir un type de préoccupation.", 'error')
      }else if(this.mat_aff == true && param.matricule.trim() == ''){
        AlertNotif.finish("Renseigner le matricule", "Champ obligatoire", 'error')
      }else if(param.idPrestation == null || param.idPrestation == ""){
        AlertNotif.finish("Erreur","Veuillez choisir une prestation.", 'error')
      }else if(!param.objet){
        AlertNotif.finish("Renseigner l'objet", "Champ obligatoire", 'error')
      }else if(!param.msgrequest){
        AlertNotif.finish("Renseigner le message", "Champ obligatoire", 'error')
      }else{
        this.loading=true
        this.requeteService.update(param, this.selected_data.id).subscribe((rest: any) => {
          this.init()
          this.visible = 0
          this.modalService.dismissAll()
          this.loading=false
          if(rest.status=="error"){
            AlertNotif.finish("Erreur",rest.message, 'error')
          }else{
            AlertNotif.finish("Modification requete", "Requête modifiée avec succès", 'success')
          }
        })
      }
  }
  chargerPrestation(event) {
    // this.services=[]
    // this.__services.forEach(item => {
    //   if (item.idType == event.target.value)
    //     this.services.push(item);
    // });
    this.services=[]
    this.prestationService.getAllType(event.target.value).subscribe((res:any)=>{
      this.services=res
    })
      
    this.thematiqueService.get(event.target.value).subscribe((res:any)=>{
      this.descrCarr = res.descr
      if(res.libelle== "Formation" || res.libelle == "Carrière"){
        this.mat_aff = true
      }else{
        this.mat_aff = false
      }
    })
  }
  genererPDF() {
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
            this.init()
            AlertNotif.finish("Suppression requete", "Suppression effectuée avec succès", 'success')
          }, (err) => {
            AlertNotif.finish("Suppression requete", "Erreur, Verifiez que vous avez une bonne connexion internet", 'error')
          })
        }
      })
  }
  editRDV(value) {
    value.statut=this.selected_data2.statut
    value.id=this.selected_data2.id
    this.loading=true
    this.rdvService.update(value,this.selected_data2.id).subscribe((res)=>{
      this.modalService.dismissAll()
      this.init()
      this.loading=false
      if(res.status=="error"){
        AlertNotif.finish("Erreur",res.message, 'error')
      }else{
        AlertNotif.finish("Nouvelle modification",  "Motification effectué avec succès", 'success')
      }
    }, (err)=>{
      AlertNotif.finish("Nouvelle modification", "Erreur, Verifiez que vous avez une bonne connexion internet", 'error')
    })
	}

  sendRDV() {
    if (this.selected_data2 == null) {
      AlertNotif.finish("Erreur", "Veuillez selectionnez un élément puis réessayer", 'error');
      return;
    }
    if (this.selected_data2.statut != 0) {
      AlertNotif.finish("Erreur", "Votre de demande est déjà en cours de traitement.", 'error');
      return;
    }
    AlertNotif.finishConfirm("Transmettre rdv",
      "Cette action est irreversible. Voulez-vous continuer ?").then((result) => {
        if (result.value) {
          var param = {
            listerdv: [ this.selected_data2.id],
            statut: 1,  // 1: transmis à la structure
            idEntite:this.selected_data2.idEntite,
           }
           
          this.rdvService.saveRdvStatut(param).subscribe((res: any) => {
            this.init()
            AlertNotif.finish("Transmettre rdv", "Suppression effectuée avec succès", 'success')
          }, (err) => {
            AlertNotif.finish("Transmettre rdv", "Erreur, Verifiez que vous avez une bonne connexion internet", 'error')
          })
        }
      })
  }
  
  dropRDV() {
    if (this.selected_data2 == null) {
      AlertNotif.finish("Erreur", "Veuillez selectionnez un élément puis réessayer", 'error');
      return;
    }
    if (this.selected_data2.statut != 0) {
      AlertNotif.finish("Erreur", "Vous ne pouvez plus supprimer cet element. Elle est déjà en cours de traitement.", 'error');
      return;
    }
    AlertNotif.finishConfirm("Suppression rdv",
      "Cette action est irreversible. Voulez-vous continuer ?").then((result) => {
        if (result.value) {
          this.rdvService.delete(this.selected_data2.id).subscribe((res: any) => {
            this.init()
            AlertNotif.finish("Suppression rdv", "Suppression effectuée avec succès", 'success')
          }, (err) => {
            AlertNotif.finish("Suppression rdv", "Erreur, Verifiez que vous avez une bonne connexion internet", 'error')
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

  saveUsager(value) {
    var param = {
      id: this.user.id,
      email: value.email,
      nom: value.nom,
      prenoms: value.prenoms,
      password:"", //value.password
      confirm: "",//value.confirm
      tel: value.tel,
      idEntite:this.selectedEntie,
      idDepartement: value.idDepartement,
      interfaceRequete: "USAGER",
      visible: this.visible
    };
    this.usagersService.update(param, this.user.id).subscribe((res: any) => {
      this.modalService.dismissAll()
      this.visible = 0
      this.init()
      AlertNotif.finish("Mise à jour", "Profile mis à jour avec succès", 'succes');
    })
    
    /*if (value.password != value.confirm) {
      AlertNotif.finish("Erreur", "Mot de passe non identique", 'error');
    } else {
     
    }*/

  }
  noterRequete(value) {
    var param = {
      codeRequete: this.selected_data_note.codeRequete,
      noteDelai: value.noteDelai,
      noteResultat: value.noteResultat,
      idEntite:this.selectedEntie,
      commentaireNotation: value.commentaireNotation,
    };
    this.loading=true
    this.requeteService.noterRequete(param).subscribe((res: any) => {
      this.modalService.dismissAll()
      this.loadRequest()
      this.loading=false
      if(res.status=="error"){
        AlertNotif.finish("Erreur",res.message, 'error')
      }else{
        AlertNotif.finish("Appreciation", "Appreciation envoyé avec succès", 'succes');
      }
    })
  }

  transmettreRequete() {
    if (this.selected_data == null) {
      AlertNotif.finish("Erreur", "Veuillez selectionnez un élément puis réessayer", 'error');
      return;
    }
    if (this.selected_data.visible == 1) {
      AlertNotif.finish("Erreur", "Vous avez déjà transmis cette requête.", 'error');
      return;
    }
    var msgConfirm = "Voulez-vous transmettre la requête ?";
    var confirmResult = confirm(msgConfirm);
    if (confirmResult === false) return;

    var param = {
      idRequete: this.selected_data.id,
      idEntite:this.selectedEntie,
      fichier_requete: this.selected_data.request_file_data,
    };

    this.requeteService.transmettreRequeteExterne(param).subscribe((res: any) => {
      this.modalService.dismissAll()
      this.loadRequest()
      AlertNotif.finish("Transmission requete", "Requete transmise avec succès", 'succes');
    })
  }

  setStatut() {
    this.statut = 1
  }
  show_structures=false
  statut=0
  saveRdv(value) {
    var param = {
      idUsager: this.user.id,
      objet: this.selected_el_obj,
      idRdvCreneau: value.idRdvCreneau,
      codeRequete: value.codeRequete,
      dateRdv: value.dateRdv,
      idEntite:this.selectedEntie,
      idStructure:value.idStructure,
      statut: this.statut,
      attente: value.attente,
    }
    if(param.idStructure==undefined){
      delete param.idStructure
    }
    this.show_structures=false
    this.loading=true
    this.rdvService.create(param).subscribe((res: any) => {
      this.modalService.dismissAll()
      this.loadRdv()
      this.statut=0
      this.loading=false
      if(res.status=="error"){
        AlertNotif.finish("Erreur",res.message, 'error')
      }else{
        if(param.statut==0){
          AlertNotif.finish("Prise de rdv", "RDV enregistré avec succès", 'succes');
        }else{
          AlertNotif.finish("Prise de rdv", "RDV enregistré et transmis avec succès", 'succes');
        }
      }
     
    })
  }
  selected_el_obj = ""

  selectRequest(event) {
    if(event.target.value!="0"){
      this.show_structures=false
      this.selected_el_obj = this.data.find(e => (e.codeRequete == event.target.value)).objet
    }else{
      this.show_structures=true
      this.selected_el_obj = ""
    }
  }

}
