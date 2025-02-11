import { Component, OnInit,ViewChild } from '@angular/core';
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
import Swal from 'sweetalert2'

@Component({
  selector: 'app-homepfc',
  templateUrl: './homepfc.component.html',
  styleUrls: ['./homepfc.component.css']
})
export class HomepfcComponent implements OnInit {
  active=1
  @ViewChild('contentRetraite') contentRetraite : any;
  @ViewChild('contentCarriere') contentCarriere : any;

  loadFile(file){
    return Config.toFile(file)
  }
  page = 1;
  pagerv = 1;
  
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
    if(localStorage.getItem('matUserData')!=undefined) this.user=this.local_service.getItem('matUserData');
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
  onglet_What = false
  mat_aff = false
  descrCarr=[]
  selected_data_note: any
  notes = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10]

  // Declaration 
  matricu_rv =""

  ngOnInit(): void {
    if (localStorage.getItem('matUserData') != null) {
      this.user = this.localStorageService.getJsonValue("matUserData")
      //Controle pour acceder à l'onglet WhatsApp
      if(this.user.attribu_com != null){
        this.onglet_What = true
      }else{
        this.onglet_What = false
      }
      
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
    
  show_step(id) {
    return this.etapes.find((e) => (e.id == id))
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
  noterRequete(value) {
    var param = {
      codeRequete: this.selected_data_note.codeRequete,
      noteDelai: value.noteDelai,
      noteResultat: value.noteResultat,
      idEntite:this.selectedEntie,
      commentaireNotation: value.commentaireNotation,
    };
    this.loading=true
    this.user_auth_service.noterRequetePfc(param).subscribe((res: any) => {
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

  openAddModal(content) {
    this.modalService.open(content, {ariaLabelledBy: 'modal-basic-title'}).result.then((result) => {
      this.closeResult = `Closed with: ${result}`;
    }, (reason) => {
      this.closeResult = `Dismissed ${this.getDismissReason(reason)}`;
    });
  }


  chargerPrestation(event){
    this.services=[]
    this.user_auth_service.getAllTypePrest(event.target.value).subscribe((res:any)=>{
      this.services=res
    })
    
    this.user_auth_service.getThema(event.target.value).subscribe((res:any)=>{
      this.descrCarr = res.descr
      if(res.libelle== "Formation" || res.libelle == "Carrière"){
        this.mat_aff = true
      }else{
        this.mat_aff = false
      }
    })
    
  }
  ChangerFile(file){
  //  window.location.href="https://api.mataccueil.gouv.bj/api/downloadFile?file="+file
    window.location.href="https://preprodmtfp.gouv.bj/pprod-mataccueilapi/api/downloadFile?file="+file
    // window.location.href="http://api.mataccueil.sevmtfp.test/api/downloadFile?file="+file
    // window.location.href="http://localhost:8003/api/downloadFile?file="+file
  }

  addRequeteusager(value){

    let service = null
    if (this.link_to_prestation==1 ) {
      service = this.services.filter(e => (e.id == value.idPrestation))[0]
    }else{
      service=this.services.filter(e => (e.hide_for_public == 1))[0]
    }

    if (this.selected_type_preoccupation == 0) {
      AlertNotif.finish("Sélectionner le type de requête", "Champ obligatoire", 'error')
    }else if(!value.idEntite){
      AlertNotif.finish("Sélectionner la structure destinatrice", "Champ obligatoire", 'error')
    }else if(!value.contactUs){
      AlertNotif.finish("Renseigner le contact", "Champ obligatoire", 'error')
    }else if(this.mat_aff == true && value.matricule == ''){
      AlertNotif.finish("Renseigner le matricule", "Champ obligatoire", 'error')
    }else if((value.idPrestation == "" || value.idPrestation == null) && this.link_to_prestation==1){
      AlertNotif.finish("Sélectionner la prestation", "Champ obligatoire", 'error')
    }else if(!value.objet){
      AlertNotif.finish("Renseigner l'objet", "Champ obligatoire", 'error')
    }else if(!value.msgrequest){
      AlertNotif.finish("Renseigner le message", "Champ obligatoire", 'error')
    }else{
      let interfa = null
      let idReg = null
      let idEsWha = null
      let natur = null
      if(this.selected_data_rvMat == null){
        interfa = this.link_to_prestation == 1 ? "USAGER"  : "SRU"
        idReg = ""
        if(this.selected_data_Whats == null){
          natur = 5 //En ligne 
        }else{
          natur = 12 //WhatsApp
          idEsWha = this.selected_data_Whats.id
        }
      }else{
        interfa = "registre"
        idReg = this.selected_data_rvMat.id
      }
      var param = {
        objet: value.objet,
        contactUs: value.contactUs,
        mailPfc: this.user.email,
        link_to_prestation:this.link_to_prestation,
        idPrestation: this.link_to_prestation==0  ? '435' : value.idPrestation, //cas d'une requete
        nbreJours: service == null ? 0 : service.nbreJours,
        msgrequest: value.msgrequest,
        idregistre : idReg,
        idEchanWhat : idEsWha,
        email:value.mailUs,
        matricule: this.mat_aff == true ? value.matricule : '',
        natureRequete:natur,
        // nom:this.user.agent_user.nomprenoms, natureRequete
        nom:"",
        tel:'',
        idDepartement:'',
        interfaceRequete: interfa,
        idUser:this.user.id,
        idEntite:value.idEntite,
        plainte: value.plainte,
        visible: this.visible
        // visible: 1
     };

     this.user_auth_service.createrequeteusager(param).subscribe((rest:any)=>{
      this.loadRequest()
      this.loadRequestrv()
      this.loadWhatsApp()
      this.visible=0
      this.modalService.dismissAll()
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
    this.selected_data_rvMat = ''
    this.selected_data_Whats = ''
    }
  }
  addReponse(value){

    if (value.reponse_whats == "") {
      AlertNotif.finish("Renseigner la réponse à cette discusion", "Champ obligatoire", 'error')
      return;
    }else{
      let formData = new FormData()
      formData.append('reponse', value.reponse_whats)
      formData.append('fichier', this.file)

      this.user_auth_service.updateWhatsReponse(formData, this.selected_data_Whats.id).subscribe((res:any)=>{
          if(res.status=="error"){
            AlertNotif.finish("Erreur",res.message, 'error')
          }else{
            this.loadWhatsApp()
            AlertNotif.finish("Réponse d'une discussion whatsapp",  "Réponse donnée avec succès", 'success')
          }
          this.modalService.dismissAll()
      })     
      this.selected_data_rvMat = ''
      this.selected_data_Whats = ''
    }
  }
  
  saveRequeteusager(value) {
    let service = null
    if (this.link_to_prestation==1 ) {
      service = this.services.filter(e => (e.id == value.idPrestation2))[0]
    }else{
      service=this.services.filter(e => (e.hide_for_public == 1))[0]
    }
    if (this.selected_type_preoccupation == 0) {
      AlertNotif.finish("Sélectionner le type de requête", "Champ obligatoire", 'error')
    }else if(!value.idEntite2){
      AlertNotif.finish("Sélectionner la structure destinatrice", "Champ obligatoire", 'error')
    }else if(!value.contactUs2){
      AlertNotif.finish("Renseigner le contact", "Champ obligatoire", 'error')
    }else if(this.mat_aff == true && value.matricul == ''){
      AlertNotif.finish("Renseigner le matricule", "Champ obligatoire", 'error')
    }else if((value.idPrestation2 == "" || value.idPrestation2 == null) && this.link_to_prestation==1){
      AlertNotif.finish("Sélectionner la prestation", "Champ obligatoire", 'error')
    }else if(!value.objet2){
      AlertNotif.finish("Renseigner l'objet", "Champ obligatoire", 'error')
    }else if(!value.msgrequest2){
      AlertNotif.finish("Renseigner le message", "Champ obligatoire", 'error')
    }else{
      var param = {
        objet: value.objet2,
        id: this.selected_data_req.id,
        contactUs: value.contactUs2,
        mailPfc: this.user.email,
        link_to_prestation:this.link_to_prestation,
        idPrestation: this.link_to_prestation==0  ? '435' : value.idPrestation2, //cas d'une requete
        nbreJours: service == null ? 0 : service.nbreJours,
        msgrequest: value.msgrequest2,
        email:value.mailUs2,
        matricule: this.mat_aff == true ? value.matricul : '',
        // nom:this.user.agent_user.nomprenoms,
        nom:"",
        tel:'',
        idDepartement:'',
        interfaceRequete: this.link_to_prestation==1 ? "USAGER"  : "SRU" ,
        idUser:this.user.id,
        idEntite:value.idEntite2,
        plainte: value.plainte2,
        visible: this.visible
     };

     this.user_auth_service.Updaterequeteusager(param, this.selected_data_req.id).subscribe((rest:any)=>{
      this.loadRequest()
      this.visible=0
      this.modalService.dismissAll()
      if(rest.status=="error"){
        AlertNotif.finish("Erreur",rest.message, 'error')
      }else{
        if(param.visible==0){
          AlertNotif.finish("Modification requête", "Requête modifiée avec succès", 'success')
        }else{
          AlertNotif.finish("Modification requête", "Requete modifiée et transmis avec succès", 'success')
        }
      }
    })     

    }
  }
  
  transmettreRequete() {
    if (this.selected_data_req == null) {
      AlertNotif.finish("Erreur", "Veuillez selectionnez un élément puis réessayer", 'error');
      return;
    }
    if (this.selected_data_req.visible == 1) {
      AlertNotif.finish("Erreur", "Vous avez déjà transmis cette requête.", 'error');
      return;
    }
    var msgConfirm = "Voulez-vous transmettre la requête ?";
    var confirmResult = confirm(msgConfirm);
    if (confirmResult === false) return;

    var param = {
      idRequete: this.selected_data_req.id,
      idEntite:this.selectedEntie,
      fichier_requete: this.selected_data_req.request_file_data,
    };

    this.user_auth_service.transmettreRequeteExterne(param).subscribe((res: any) => {
      this.modalService.dismissAll()
      this.loadRequest()
      AlertNotif.finish("Transmission requête", "Requête transmise avec succès", 'success');
    })
  }
  addRequeteRv(value){
    if (value.contactMatri == "") {
      AlertNotif.finish("Renseigner le matricule ou le contact du visiteur", "Champ obligatoire", 'error')
    }else if(!value.plainterv){
      AlertNotif.finish("Sélectionner le type", "Champ obligatoire", 'error')
    }else if(value.nom_pre_rv == ""){
      AlertNotif.finish("Renseigner le nom et prénom (s) du visiteur", "Champ obligatoire", 'error')
    }else if(!value.idEntite){
      AlertNotif.finish("Sélectionner la structure destinatrice", "Champ obligatoire", 'error')
    }else if((value.preoccurv == "" || value.preoccurv == null)){
      AlertNotif.finish("Renseigner la préoccupation du visiteur", "Champ obligatoire", 'error')
    }else if(value.satisfaitrv == ""){
      AlertNotif.finish("Sélectionner une appréciation", "Champ obligatoire", 'error')
    }else if(value.satisfaitrv && value.observarv == ""){
      AlertNotif.finish("Renseigner l'observation", "Champ obligatoire", 'error')
    }else{
      var param = {
        contactMatri: value.contactMatri,
        plainterv: value.plainterv,
        nom_pre_rv: value.nom_pre_rv,
        idEntite:value.idEntite,
        preoccurv:value.preoccurv,
        idUser:this.user.id,
        satisfaitrv: value.satisfaitrv,
        created_at: this.dateACloture =="" ? new Date() : new Date(this.dateACloture),
        observarv: value.observarv,
     };

     this.user_auth_service.createrequeteVisite(param).subscribe((res:any)=>{
      if(res.status=="error"){
        AlertNotif.finish("Erreur",res.message, 'error')
      }else{
        this.loadRequestrv()
        
        AlertNotif.finish("Ajout requête",  "Visite ajoutée avec succès", 'success')
      }
      this.modalService.dismissAll()
    })     
    }
  }

  addWhatsApp(value){
    if (value.contWhatsapp == "") {
      AlertNotif.finish("Renseigner le contact WhatsApp", "Champ obligatoire", 'error')
    }else if(value.discussiontxt == ""){
      AlertNotif.finish("Renseigner la discussion", "Champ obligatoire", 'error')
    }else{
      var param = {
        contWhatsapp: value.contWhatsapp,
        discussiontxt: value.discussiontxt,
        idUser:this.user.id,
     };
      this.user_auth_service.createDiscussionWhats(param).subscribe((res:any)=>{
        if(res.status=="error"){
          AlertNotif.finish("Erreur",res.message, 'error')
        }else{
          this.loadWhatsApp()
          AlertNotif.finish("Ajout d'une discussion whatsapp",  "Discussions ajoutée avec succès", 'success')
        }
        this.modalService.dismissAll()
      })     

    }
  }
  addClotureRv(value){
    
    // if(this.file == ""){
    //   AlertNotif.finish("Joindre le fichier", "Champ obligatoire", 'error')
    // }else{
    
     let formData = new FormData()
     formData.append('id_user', this.user.id)
     formData.append('date_cloture', this.dateACloture)
     formData.append('nbrvisite', value.nbrevisite)
     formData.append('fichier', this.file)
     this.user_auth_service.CloturerequeteVisite(formData).subscribe((res:any)=>{
       
      if(res.status=="error"){
        AlertNotif.finish("Erreur",res.message, 'error')
      }else{
        this.loadRequestrv()
        AlertNotif.finish("Clôture de registre", "Opération effectuée avec succès", 'success')
      }
      this.file = ""
      this.dateACloture = ""
      this.modalService.dismissAll()
    })     

    // }
  }
  UpdateRequeteRv(value){
    
    if (value.contactMatri == "") {
      AlertNotif.finish("Renseigner le matricule ou le contact du visiteur", "Champ obligatoire", 'error')
    }else if(!value.plainterv){
      AlertNotif.finish("Sélectionner le type", "Champ obligatoire", 'error')
    }else if(value.nom_pre_rv == ""){
      AlertNotif.finish("Renseigner le nom et prénom (s) du visiteur", "Champ obligatoire", 'error')
    }else if(!value.idEntite){
      AlertNotif.finish("Sélectionner la structure destinatrice", "Champ obligatoire", 'error')
    }else if((value.preoccurv == "" || value.preoccurv == null)){
      AlertNotif.finish("Renseigner la préoccupation du visiteur", "Champ obligatoire", 'error')
    }else if(value.satisfaitrv == ""){
      AlertNotif.finish("Sélectionner une appréciation", "Champ obligatoire", 'error')
    }else if(value.satisfaitrv && value.observarv == ""){
      AlertNotif.finish("Renseigner l'observation", "Champ obligatoire", 'error')
    }else{
      var param = {
        contactMatri: value.contactMatri,
        plainterv: value.plainterv,
        nom_pre_rv: value.nom_pre_rv,
        idEntite:value.idEntite,
        preoccurv:value.preoccurv,
        idUser:this.user.id,
        satisfaitrv: value.satisfaitrv,
        observarv: value.observarv,
     };

     this.user_auth_service.updaterequeteVisite(param, this.selected_data_reqrv.id).subscribe((res:any)=>{
        if(res.status=="error"){
          AlertNotif.finish("Erreur",res.message, 'error')
        }else{
          this.loadRequestrv()
          AlertNotif.finish("Ajout requête",  "Visite ajoutée avec succès", 'success')
        }
        this.modalService.dismissAll()
    })     

    }
  }
  UpdateRequeteWhats(value){
    if (value.contWhatsapp == "") {
      AlertNotif.finish("Renseigner le contact WhatsApp", "Champ obligatoire", 'error')
    }else if(value.discussiontxt == ""){
      AlertNotif.finish("Renseigner la discussion", "Champ obligatoire", 'error')
    }else{
      var param = {
        contWhatsapp: value.contWhatsapp,
        discussiontxt: value.discussiontxt,
        idUser:this.user.id,
     };
     this.user_auth_service.updateWhatsapp(param, this.selected_data_Whats.id).subscribe((res:any)=>{
          if(res.status=="error"){
            AlertNotif.finish("Erreur",res.message, 'error')
          }else{
            this.loadWhatsApp()
            AlertNotif.finish("Modification d'une discussion whatsapp",  "Discussions modifiée avec succès", 'success')
          }
          this.modalService.dismissAll()
      })     
    }
    this.selected_data_Whats = '';
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
      (res:any)=>{ },
      (err)=>{
          this.loading=false;
          // console.log(err)
         // AlertNotif.finish("Applications","Echec de récupération des applications","error")
        }
  )
  }

  init(page){
    this.selected_traiteWha = 'non'

    // console.log(this.user)
    this.loadRequest()
    this.loadRequestrv()
    this.loadWhatsApp()
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

    this.institutions=[]
    this.user_auth_service.getAllInstitu().subscribe((res: any) => {
     this.institutions = res
     })

     this.prepare(this.user.idEntite)
  }

  file: string | Blob =""
  onFileChange(event:any) {
    if (event.target.files.length > 0) {
      this.file = event.target.files[0];
    }
  }



  openEditModalCloture(content){
   
    if (this.dateACloture == "") {
      AlertNotif.finish("Erreur", "Veuillez selectionnez la date à clôturer", 'error');
      return;
    }
    this.modalService.open(content, {ariaLabelledBy: 'modal-basic-title', size: 'lg'}).result.then((result) => {
      this.closeResult = `Closed with: ${result}`;
    }, (reason) => {
      this.closeResult = `Dismissed ${this.getDismissReason(reason)}`;
    });
  }

  openEditModalVisi(content){

    if (this.nbreDay.length != 0 && this.dateACloture == "") {
      AlertNotif.finish("Erreur", "Veuillez clôturer les régistres des dates précédentes", 'error');
      return;
    }
    this.modalService.open(content, {ariaLabelledBy: 'modal-basic-title', size: 'lg'}).result.then((result) => {
      this.closeResult = `Closed with: ${result}`;
    }, (reason) => {
      this.closeResult = `Dismissed ${this.getDismissReason(reason)}`;
    });
  }

  openEditModalWhatsApp(content){

    this.modalService.open(content, {ariaLabelledBy: 'modal-basic-title', size: 'lg'}).result.then((result) => {
      this.closeResult = `Closed with: ${result}`;
    }, (reason) => {
      this.closeResult = `Dismissed ${this.getDismissReason(reason)}`;
    });
  }

  openEditModalAdd(content){
    this.selected_data_Whats = null
    this.selected_data_rvMat = null
    this.modalService.open(content, {ariaLabelledBy: 'modal-basic-title', size: 'lg'}).result.then((result) => {
      this.closeResult = `Closed with: ${result}`;
    }, (reason) => {
      this.closeResult = `Dismissed ${this.getDismissReason(reason)}`;
    });
  }

  openEditModal(content){
    
    this.modalService.open(content, {ariaLabelledBy: 'modal-basic-title', size: 'lg'}).result.then((result) => {
      this.closeResult = `Closed with: ${result}`;
    }, (reason) => {
      this.closeResult = `Dismissed ${this.getDismissReason(reason)}`;
    });
  }

  openEditModalAddRv(content){
    this.selected_data_Whats = null
    this.modalService.open(content, {ariaLabelledBy: 'modal-basic-title', size: 'lg'}).result.then((result) => {
      this.closeResult = `Closed with: ${result}`;
    }, (reason) => {
      this.closeResult = `Dismissed ${this.getDismissReason(reason)}`;
    });
  }

  openEditModalLg(content){
    if (this.selected_data_req == null) {
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
searchTextReqrv=""
searchTextWhats=""
selected_Status=""
selected_satist=""
selected_traiteWha=""

show_actions=true;
show_actionsrv=true;
show_actionsWhats=true;
selected_data_req: any
selected_data_reqrv: any
selected_data_Whats: any
selected_data_rvMat: any
selectedEntie=null
visible = 0
pageSize = 10;
pageSizerv = 100;
collectionSize = 0;
collectionSizerv = 0;
collectionSizeWhats = 0;
dataReq:any
dataReqrv:any
dataWhats:any
etapes = []
_tempReq: any[]=[];
_tempReqrv: any[]=[];
_tempWhats: any[]=[];
// pageReq = 1;
institutions = []
nbreDay:any
dateACloture = ""

searchReq() {
  this.dataReq = this._tempReq.filter(r => {
    const term = this.searchTextReq.toLowerCase();
    const stat = this.selected_Status
    return (r.objet.toLowerCase().includes(term) || r.msgrequest.toLowerCase().includes(term)) && r.traiteOuiNon == stat
  })
  this.collectionSize = this.dataReq.length
}

searchReqrv() {
  this.dataReqrv = this._tempReqrv.filter(r => {
    const term = this.searchTextReqrv.toLowerCase();
    const stat = this.selected_satist
    if(this.dateACloture == ""){
      return (r.nom_prenom.toLowerCase().includes(term) 
              || r.matri_telep.toLowerCase().includes(term) 
              || r.contenu_visite.toLowerCase().includes(term)) 
              &&  r.satisfait.toLowerCase().includes(stat)
    }else{
      return (r.nom_prenom.toLowerCase().includes(term) 
              || r.matri_telep.toLowerCase().includes(term) 
              || r.contenu_visite.toLowerCase().includes(term)) 
              &&  r.satisfait.toLowerCase().includes(stat)
              &&  r.created_at.includes(this.dateACloture)
    }
  })
  this.collectionSizerv = this.dataReqrv?.length
}

searchWhats() {
  this.dataWhats = this._tempWhats.filter(r => {
    const term = this.searchTextWhats.toLowerCase();
    const trai = this.selected_traiteWha
      return (r.numerowhatsapp.toLowerCase().includes(term) 
              || r.discussions.toLowerCase().includes(term)) 
  })
  // const term = this.searchTextWhats.toLowerCase();
  // this.user_auth_service.getAllForWhatsapp(1,term,this.selected_traiteWha).subscribe((res: any) => {
  //   this.dataWhats = res
  //   this._tempWhats = this.dataWhats
  //   this.collectionSizeWhats = this.dataWhats?.length
  // })
  this.collectionSizeWhats = this.dataWhats?.length
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
  this.user_auth_service.getAllForPfcom(this.user.id, 1).subscribe((res: any) => {
      this.dataReq = res
      this._tempReq = this.dataReq
      this.collectionSize = this.dataReq.length
    })
    this.ChechEtape();
}

loadRequestrv() {
  // console.log(this.user)
  this._tempReqrv = []
  this.dataReqrv = []
  this.user_auth_service.getAllForRegistreVis(this.user.id, 1).subscribe((res: any) => {
      this.dataReqrv = res
      this._tempReqrv = this.dataReqrv
      this.collectionSizerv = this.dataReqrv?.length
    })
    // this.ChechEtape();
 
    this.nbreDay=[]
    this.user_auth_service.getListDay(this.user.id).subscribe((res: any) => {
      res.forEach((element:any) => {
        let date= new Date(element);
        let dayOfWeek =date.getDay();

        if (dayOfWeek!=6 && dayOfWeek!=0) {
          this.nbreDay.push(element)
        }
      });
     this.gotoHashtag('nav-tabs')
     })
    }
    
    gotoHashtag(fragment: string) {
      
      if(this.nbreDay.length != 0){
        AlertNotif.MsgToast().fire({
            icon: 'info',
            title: 'Vous aviez '+this.nbreDay.length+' date(s) non cloturée(s).'
          })
        setTimeout(function () {
            const element: any = document.querySelector("." + fragment);
            if (element) {
                element.scrollIntoView();
                
            }
        })
      }
    }

loadWhatsApp() {
  // console.log(this.user)
  this._tempWhats = []
  this.dataWhats = []
  this.user_auth_service.getAllForWhatsapp(1,this.selected_traiteWha).subscribe((res: any) => {
      this.dataWhats = res
      this._tempWhats = this.dataWhats
      this.collectionSizeWhats = this.dataWhats?.length
    })
    this.searchTextWhats = ''
}
prepare(idEntite){
  
  this.structures = []
  this.user_auth_service.getAllServ(1,idEntite).subscribe((res:any)=>{
    this.structures = res
  })

 this.natures=[]
  this.user_auth_service.getAllNatu(idEntite).subscribe((res:any)=>{
    this.natures=res
  })

  this.themes=[]
  this.user_auth_service.getAllThe(idEntite).subscribe((res:any)=>{
    this.themes=res
  })
}
onEntiteChange(event){
  this.selectedEntie=+event.target.value
  this.prepare(this.selectedEntie)
}

ChechEtape(){
  this.etapes = []
  this.user_auth_service.getAllEtap(0).subscribe((res: any) => {
    this.etapes = res
  })
}

setVisible() {
  this.visible = 1
}

checkedReq(event, el) {
  this.selected_data_req = el
  console.log('-----------------------')
  console.log(el)
  this.selected_type_preoccupation = el.plainte
  this.link_to_prestation = el.link_to_prestation
  if(el.visible==0){
    this.show_actions=true
  }else{
    this.show_actions=false
  }
  this.selectedEntie = el.idEntite
  
  this.user_auth_service.getAllThe(this.selected_data_req.idEntite).subscribe((res: any) => {
    this.themes = res
  })
  if(this.selected_data_req.service.type.libelle== "Formation" || this.selected_data_req.service.type.libelle == "Carrière"){
    this.mat_aff = true
  }else{
    this.mat_aff = false
  }
  // this.user_auth_service.getThema(this.selected_data_req).subscribe((res:any)=>{
  //   this.descrCarr = res.descr
  // })
  this.user_auth_service.getAllTypePrest(this.selected_data_req.service.idType).subscribe((res:any)=>{
    this.services=res
  })
}

checkedReqrv(event, el) {
  this.selected_data_reqrv = el
}

checkedWhatsap(event, el) {
  
  this.selected_data_Whats = el
}

checkedRv_Mat(event, el) {
  
  this.selected_data_rvMat = el
  this.selected_type_preoccupation = el.plainte
}


openEditModalrv(content) {
  this.loading=false
  if (this.selected_data_reqrv != null) {
    // this.prepare(this.selectedEntie)
    this.modalService.open(content, { ariaLabelledBy: 'modal-basic-title', size: "lg" }).result.then((result) => {
      this.closeResult = `Closed with: ${result}`;
    }, (reason) => {
      this.closeResult = `Dismissed ${this.getDismissReason(reason)}`;
    });
  } else {
    AlertNotif.finish("Erreur", "Veuillez selectionnez un élément puis réessayer", 'error')
  }
}

openEditModalWhats(content) {
  this.loading=false
  if (this.selected_data_Whats != null) {

    this.modalService.open(content, { ariaLabelledBy: 'modal-basic-title', size: "lg" }).result.then((result) => {
      this.closeResult = `Closed with: ${result}`;
    }, (reason) => {
      this.closeResult = `Dismissed ${this.getDismissReason(reason)}`;
    });
  } else {
    AlertNotif.finish("Erreur", "Veuillez selectionnez un élément puis réessayer", 'error')
  }
}

dropRequeteusagerrv() {

  if (this.selected_data_reqrv == null) {
    AlertNotif.finish("Erreur", "Veuillez selectionnez un élément puis réessayer", 'error');
    return;
  }else if (this.selected_data_reqrv.cloture == 1) {
    AlertNotif.finish("Erreur", "Impossible de supprimer ce régistre car il est déjà cloturé", 'error');
    return;
  }
  AlertNotif.finishConfirm("Suppression cette visite",
    "Cette action est irreversible. Voulez-vous continuer ?").then((result) => {
      if (result.value) {
        this.user_auth_service.deleteRegistreVis(this.selected_data_reqrv.id).subscribe((res: any) => {
          this.loadRequestrv()
          AlertNotif.finish("Suppression visite", "Suppression effectuée avec succès", 'success')
        }, (err) => {
          AlertNotif.finish("Suppression visite", "Erreur, Verifiez que vous avez une bonne connexion internet", 'error')
        })
      }
    })
}

dropDiscussionWhat() {
  if (this.selected_data_Whats == null) {
    AlertNotif.finish("Erreur", "Veuillez selectionnez un élément puis réessayer", 'error');
    return;
  }
  AlertNotif.finishConfirm("Suppression cette discussion", "Cette action est irreversible. Voulez-vous continuer ?").then((result) => {
      if (result.value){
        this.user_auth_service.deleteDiscWhats(this.selected_data_Whats.id).subscribe((res: any) => {
          this.loadWhatsApp()
          AlertNotif.finish("Suppression discussions", "Suppression effectuée avec succès", 'success')
        }, (err) => {
          AlertNotif.finish("Suppression discussions", "Erreur, Verifiez que vous avez une bonne connexion internet", 'error')
        })
      }
    })
}

ConfirmerTraitement(el) {
  
  AlertNotif.finishConfirm("Confirmer l'ajout", "Souhaitez-vous ajouter cette discussion dans mataccueil ?").then((result) => {
      if (result.value){
        this.user_auth_service.ConfirmerDiscWhats(this.user.id, el.id).subscribe((res: any) => {
          if(res.status=="error"){
            AlertNotif.finish("Erreur",res.message, 'error')
          }else{
            this.loadWhatsApp()
            AlertNotif.finish("Confirmer l'ajout", "Confirmation effectuée avec succès", 'success')
          }
          
        }, (err) => {
          AlertNotif.finish("Confirmer l'ajout", "Erreur, Verifiez que vous avez une bonne connexion internet", 'error')
        })
      }
    })
}


}
