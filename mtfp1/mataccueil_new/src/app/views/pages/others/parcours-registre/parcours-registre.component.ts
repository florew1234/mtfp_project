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
import { NatureRequeteService } from '../../../../core/_services/nature-requete.service';
import { ThemeService } from 'ng2-charts';
import { TypeService } from '../../../../core/_services/type.service';


@Component({
  selector: 'app-parcours-registre',
  templateUrl: './parcours-registre.component.html',
  styleUrls: ['./parcours-registre.component.css']
})
export class ParcoursRegistreComponent implements OnInit {


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
  cpt = 0;
  nbr = 0;
  selected = [];
  current_permissions: any[] = []
  selected_data: any
  isSended = false
  selected_Status=""
  nbre: number = 0

  // checked(event, el) {
  //   console.log(el)
  //   this.selected_data = el
  //   // this.usager_full_name=this.selected_data.usager.nom+" "+this.selected_data.usager.prenoms
  //   this.RelanceAWho = ""
  //   this.ValStruRelance = ""
  //   if (this.selected_data.finalise == 1) {
  //     return;
  //   }
  //   this.cpt = 0
  //   if (this.selected_data.affectation.length > 0) {
  //     this.selected_data.affectation.forEach(item => {
  //       this.cpt++;
  //       if (this.cpt == this.selected_data.affectation.length && item.idStructure != this.selected_pfc){
  //            this.RelanceAWho = item.typeStructure;
  //            this.ValStruRelance = item.idStructure;
  //         }
  //     })
  //   }
  //   this.cpt = 0
  //   if (this.selected_data.parcours.length > 0) {
  //     this.selected_data.parcours.forEach(item => {
  //       this.cpt++;
  //       if (this.cpt == this.selected_data.parcours.length && item.idStructure == this.selected_pfc){
  //         this.RelanceAWho = ""
  //         this.ValStruRelance = ""
  //         }
  //     })
  //   }
  // }

  search() {
    this.data = []
    this._temp = []
    this.requeteService.getParcoursRegistre(this.user.idEntite,this.searchText, this.selected_idcom,this.page,this.selected_Status,null,null,this.selected_iduse).subscribe((res: any) => {
        this.spinner.hide();
        this.data = res.data
        this._temp = this.data
        this.subject.next(res);
      })
  }

  list_parcours=[]

  openEditModal(content,el){
    this.list_parcours=el
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

  user: any

  constructor(
    private modalService: NgbModal,
    private userService: UserService,
    private router: Router,
    private translate: TranslateService,

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


  etapes = []
  services = []
  departements = []
  structureservices = []
  listpfc = []
  listComm = []
  listuser = []
  // themes = []
  // natures = []

  isGeneralDirector = false
  isAdmin = false
  RelanceAWho = ""
  ValStruRelance = ""
  
  show_step(id) {
    return this.etapes.find((e) => (e.id == id))
  }

  key_type_req = ""

  apercuImage(){
    //Controle si les dates sont identiques 
    if(this.selected_idcom == "" ){
      AlertNotif.finish("Erreur", "Veuillez choisir la commune concernée", 'error');
      return
    }
    if(this.selected_iduse == "" ){
      AlertNotif.finish("Erreur", "Veuillez choisir l'acteur concerné'", 'error');
      return
    }
    if((this.select_date_start != this.select_date_end) || this.select_date_start == "" || this.select_date_end == ""){
      AlertNotif.finish("Erreur", "La date début et fin doivent être identique pour faire un aperçu du régistre physique d'une journée", 'error');
      return
    }
    var url= Config.toApiUrl('apercuimage')
    if(this.selected_idcom) url+="?idcom="+this.selected_idcom //Statut de satisfaction
    if(this.select_date_start) url+="&date="+this.select_date_start //Entite
    if(this.selected_iduse) url+="&iduser="+this.selected_iduse //Rechercher 
    window.open(url, "_blank")  
  }

  print(){
    var url= Config.toApiUrl('print-registre')
    if(this.user) url+="?ie="+this.user.idEntite //Entite
    if(this.selected_iduse) url+="&iduser="+this.selected_iduse //Rechercher 
    if(this.selected_idcom) url+="&ic="+this.selected_idcom //
    if(this.selected_Status) url+="&s="+this.selected_Status //Statut de satisfaction
    if(this.select_date_start) url+="&db="+this.select_date_start // Date debut 
    if(this.select_date_end) url+="&df="+this.select_date_end  //Date fin 
    window.open(url, "_blank")  
  }

  printstat(){
    var url= Config.toApiUrl('print-registre-stat')
    // if(this.user) url+="?ie="+this.user.idEntite //ie = Entite
    // if(this.searchText) url+="&se="+this.searchText //Rechercher
    if(this.user) url+="?u="+this.user.idagent //id_user
    // if(this.selected_Status) url+="&s="+this.selected_Status //Statut de satisfaction
    if(this.select_date_start) url+="&db="+this.select_date_start // Date debut 
    if(this.select_date_end) url+="&df="+this.select_date_end  //Date fin 
    if(this.selected_idcom) url+="&ic="+this.selected_idcom //
    window.open(url, "_blank")  
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
      if (this.user.profil_user.CodeProfil === 2) { //Administrateur
        this.isAdmin = true;
      } else {
        this.isAdmin = false;
      }
    }


    this.subject.subscribe((val) => {
      this.pager = val
      this.page = this.pager.current_page

      let pages = []
      if (this.pager.last_page <= 5) {
        for (let index = 1; index <= this.pager.last_page; index++) {
          pages.push(index)
        }
      }else{
        let start = (this.page > 3 ? this.page - 2 : 1)
        let end = (this.page + 2 < this.pager.last_page ? this.page + 2 : this.pager.last_page)
        for (let index = start; index <= end; index++) {
          pages.push(index)
        }
      }

      this.pager.pages = pages
    });
    this.listComm = []
    this.structureService.getLisCommune(this.user.idagent).subscribe((res:any)=>{
      this.listComm = res
    })

    // this.listpfc = []
    // this.structureService.getPfc().subscribe((res:any)=>{
    //   this.listpfc = res
    // })
 
  }

  onUserChange(event){
    this.listuser = []
    this.structureService.getLisUsersParCommune(+event.target.value).subscribe((res:any)=>{
      this.listuser = res
    })
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
    this.requeteService.getParcoursRegistre(this.user.idEntite,null, this.selected_idcom,page,this.selected_Status,null,null,this.selected_iduse).subscribe((res: any) => {
        this.spinner.hide();
        this.data = res.data
        this._temp = this.data
        this.subject.next(res);
      })


    // this.listpfc = []
    // this.structureService.getPfc().subscribe((res:any)=>{
    //   this.listpfc = res
    // })
    this.listComm = []
    this.structureService.getLisCommune(this.user.idagent).subscribe((res:any)=>{
      this.listComm = res
    })
    console.log(this.user)
  }

  selected_idcom=""
  selected_iduse=""
  select_date_start=""
  select_date_end=""
  filter(value){
    
    this.data = []

    this.requeteService.getParcoursRegistre(this.user.idEntite,null,value.listComm,this.page,value.statut,this.select_date_start,this.select_date_end,value.listuser).subscribe((res: any) => {
      this.spinner.hide();
      this.data = res.data
      this._temp = this.data
      this.subject.next(res);
    })
  }
  reset(){
    this.selected_idcom=""
    this.selected_iduse=""
    this.select_date_start=""
    this.select_date_end=""
   this.init(this.page) 
  }

  decomposeDate(datetime){
    let full_date=datetime.split(' ')[0]
    return {
      month:+full_date.split('/')[1] - 1,
      day:+full_date.split('/')[0],
      year:+full_date.split('/')[2]
    }
  }
  decomposeReverseDate(datetime){
    let full_date=datetime.split(' ')[0]
    return {
      month:+full_date.split('/')[1] - 1,
      day:+full_date.split('/')[2],
      year:+full_date.split('/')[0]
    }
  }
  daysTodayFromDate(checkdate) {
    let timeDiff = 0
    const date = new Date(this.decomposeDate(checkdate).year,this.decomposeDate(checkdate).month,this.decomposeDate(checkdate).day);
    timeDiff = (new Date()).getTime() - date.getTime();

    let daysdiff = Math.ceil(timeDiff / (1000 * 3600 * 24))
    return daysdiff;
  }
  daysBetweenTwoDate(date2, date1) {
    let timeDiff = 0
    var date4 = new Date(this.decomposeReverseDate(date2).year,this.decomposeReverseDate(date2).month,this.decomposeReverseDate(date2).day);
    var date3 = new Date(this.decomposeDate(date1).year,this.decomposeDate(date1).month,this.decomposeDate(date1).day);

    timeDiff = date3.getTime() - date4.getTime();
    let daysdiff = Math.ceil(timeDiff / (1000 * 3600 * 24))
    return daysdiff;
  }

  ratioBetweenTwoDate(delaiTh, date2, date1) {
    var date4 = new Date(this.decomposeReverseDate(date2).year,this.decomposeReverseDate(date2).month,this.decomposeReverseDate(date2).day);
    var date3 = new Date(this.decomposeDate(date1).year,this.decomposeDate(date1).month,this.decomposeDate(date1).day);

    var timeDiff = Math.abs(date4.getTime() - date3.getTime());
    var dayDifference = Math.ceil(timeDiff / (1000 * 3600 * 24));
    var ratio = dayDifference / delaiTh;
    return ratio;
  }
  ratioTodayFromDate(delaiTh, date) {
    var date2 = new Date();
    var date1 = new Date(this.decomposeDate(date).year,this.decomposeDate(date).month,this.decomposeDate(date).day);
    var timeDiff = Math.abs(date2.getTime() - date1.getTime());
    var dayDifference = Math.ceil(timeDiff / (1000 * 3600 * 24));
    var ratio = dayDifference / delaiTh;

    return ratio;
  }

}
