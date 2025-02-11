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
import { RelanceService } from '../../../../core/_services/relance.service';


@Component({
  selector: 'app-list-stat-structure',
  templateUrl: './list-stat-structure.component.html',
  styleUrls: ['./list-stat-structure.component.css']
})
export class ListStatStructureComponent implements OnInit {

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
  pageSize = 20;



  data2: any[] = [];
  _temp2: any[] = [];
  collectionSize2 = 0;
  page2 = 1;
  pageSize2 = 20;


  default_msg = " Votre structure dont vous avez la charge a reçue des préoccupations venant de la part des usagers du MTFP. Vous êtes priez de traiter ses préoccupations dans les plus bref délais. Merci !"

  search() {
    this.data = this._temp.filter(r => {
      const term = this.searchText.toLowerCase();
      return r.libelle.toLowerCase().includes(term)
    })
    this.collectionSize = this.data.length
  }
  SelectedidStructure = null

  openAddModal(content, idStructure) {
    this.SelectedidStructure = idStructure
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
    private etapeService: EtapeService,
    private requeteService: RequeteService,
    private relanceService: RelanceService,
    private localService: LocalService,
    private prestationService: ServiceService,
    private structureService: StructureService,
    private natureService: NatureRequeteService,
    private thematiqueService: TypeService,
    private usagersService: UsagerService,
    private spinner: NgxSpinnerService,
    private activatedRoute: ActivatedRoute,
  ) { }


  typeRequete = ""

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

  ngOnInit(): void {

    if (localStorage.getItem('mataccueilUserData') != null) {
      this.user = this.localService.getJsonValue('mataccueilUserData')
      this.prepare()

      this.router.events
        .subscribe(event => {

          if (event instanceof NavigationStart) {
            this.prepare()
          }
        })
    }



  }
  subject = new Subject<any>();
  subject2 = new Subject<any>();

  prepare() {
    this.init()

    // this.typeRequete = this.checkType().name;
    if(this.selected_type =="0"){
      this.typeRequete = 'Requetes'
    }else if(this.selected_type =="1"){
      this.typeRequete = 'Plaintes'
    }else if(this.selected_type =="2"){
      this.typeRequete = "Demandes d'informations"
    }

    this.activatedRoute.queryParams.subscribe(x => this.init());
    this.subject.subscribe((val) => {
      this.data = []
      val.forEach((e) => { 
        if (this.checkStructureHaveService(e.id, this.all_structures)) { this.data.push(e) } 
      })
      // this.typeRequete = this.checkType().name;

    })
    this.subject2.subscribe((val) => {
      this.data2 = []
      this.data2 = val
      // this.typeRequete = this.checkType().name;

    })
  }

  param_stat_hebdo = { "user": "all", startDate: "all", endDate: "all", stats: [], typeRequete: this.typeRequete, sended: 0, typeStat: "Structure" }

  checkStructureHaveService(idCheck, list:any) {
    let result: any = list.filter((e: any) => (e.id == idCheck && e.services.length != 0))
    if (result.length!=0) {
      return true
    } else {
      return false;
    }
  }
  all_structures=[]

  init() {
    if(this.selected_type == ""){
      this.selected_type = "0"
    }
    this.structures = []
    /*
    this.structureService.getAll(1, this.user.idEntite).subscribe((list: any) => {
      this.structures = list
    })*/
    this.all_structures = []
    this.structureService.getAll(0, this.user.idEntite).subscribe((list: any) => {
      this.all_structures = list
      list.forEach((e) => { 
        if (e.services.length!=0) { this.structures.push(e) } 
      })
      this._temp = []
      this.data = []
      this.requeteService.getStatByStructure(
        this.selected_type, this.user.idEntite
      ).subscribe((res: any) => {
        this.spinner.hide();
        //e.idParent==0
        res.forEach((e) => { 
          if (this.checkStructureHaveService(e.id, list)) { this.data.push(e) } 
        })
        this._temp = this.data
        this.subject.next(res);
        this.param_stat_hebdo.stats = this.data
        this.param_stat_hebdo.startDate = "all"
        this.param_stat_hebdo.endDate = "all"
        this.collectionSize = this.data.length

      })

      this._temp2 = []
      this.data2 = []
      this.requeteService.getStatAllStructure(
        this.selected_type, this.user.idEntite
      ).subscribe((res: any) => {
        this.data2 = res
        this.subject2.next(res);
        this._temp2 = this.data2
        this.collectionSize2 = this.data2.length

      })

    })

  }

  structures = []
  selected_type = ""
  param_stat = { "user": "all", "plainte": this.selected_type, startDate: "", endDate: "" }

  selected_Struct = ""
  filterAll(event) {
    if (event.target.value != "all") {
      this.data = []
      this.data = this._temp.filter((e) => e.id == +event.target.value)
      this.collectionSize = this.data.length
      this.data2 = []
      this.data2 = this._temp2.filter((e) => e.idParent == +event.target.value)
      this.collectionSize2 = this.data2.length
    } else {
      this.data = []
      this.data = this._temp
      this.collectionSize = this.data.length
      this.data2 = []
      this.data2 = this._temp2
      this.collectionSize2 = this.data2.length
    }

  }
  searchStats() {
    this._temp = []
    this.data = []
    this.param_stat.plainte = this.selected_type
    this.requeteService.filterStatByStructure(
      this.param_stat, this.user.idEntite
    ).subscribe((res: any) => {
      this.spinner.hide();
      res.forEach((e) => { if (e.idParent == 0) { this.data.push(e) } })
      this._temp = this.data
      this.param_stat_hebdo.stats = this.data
      this.param_stat_hebdo.startDate = this.param_stat.startDate
      this.param_stat_hebdo.endDate = this.param_stat.endDate
      this.collectionSize = this.data.length

      this.data2 = []
      res.forEach((e) => { if (e.idParent != 0) { this.data2.push(e) } })
      this.collectionSize2 = this.data2.length
    })
  }



  resetStats() {
    this.init()
    this.param_stat = { "user": "all", "plainte": this.selected_type, startDate: "all", endDate: "all" }
  }

  genererPDFStat(sended) {
    this.param_stat_hebdo.sended = sended
    this.prestationService.genPdfStatHebdo(this.param_stat_hebdo).subscribe((res: any) => {
      window.open(Config.toFile('statistiques/' + res.url))
    })
  }
  genererPDFStatDetails(sended) {
    this.param_stat_hebdo.sended = sended
    this.param_stat_hebdo.stats = this.data2
    this.prestationService.genPdfStatHebdo(this.param_stat_hebdo).subscribe((res: any) => {
      window.open(Config.toFile('statistiques/' + res.url))
    })
  }

  sendRelance(value) {
    value.idEntite = this.user.idEntite
    value.idStructure = this.SelectedidStructure
    this.relanceService.create(value).subscribe((res) => {
      this.modalService.dismissAll()
      AlertNotif.finish("Relance", "Relance envoyée avec succès", 'success');
    })

  }
}
