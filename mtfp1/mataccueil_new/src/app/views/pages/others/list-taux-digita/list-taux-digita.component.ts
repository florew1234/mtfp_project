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
  selector: 'app-list-taux-digita',
  templateUrl: './list-taux-digita.component.html',
  styleUrls: ['./list-taux-digita.component.css']
})
export class ListauxDigitComponent implements OnInit {

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


  search() {
    this.data = this._temp.filter(r => {
      const term = this.searchText.toLowerCase();
      return r.libelle.toLowerCase().includes(term)
    })
    this.collectionSize = this.data.length
  }
  SelectedidStructure = null

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
    // this.activatedRoute.queryParams.subscribe(x => this.init());
    // this.subject.subscribe((val) => {
    //   this.data = val
    // })
  }

  all_structures=[]

  init() {
    if(this.selected_type == ""){
      this.selected_type = "0"
    }
    this.structures = []
    this.requeteService.getAll_Structure(this.user.idEntite).subscribe((res: any) => {
      this.spinner.hide();
      this.data = res
      this._temp = this.data
      this.subject.next(res);
      this.collectionSize = this.data.length
      console.log('-------------')
      console.log(res)
    })
    // this.all_structures = []
    // this.structureService.getAll(0, this.user.idEntite).subscribe((list: any) => {
    //   this.all_structures = list
    //   list.forEach((e) => { 
    //     if (e.services.length!=0) { this.structures.push(e) } 
    //   })
    //   this._temp = []
    //   this.data = []
      
    // })

  }

  structures = []
  selected_type = ""

  selected_Struct = ""
  filterAll(event) {
    if (event.target.value != "all") {
      this.data = []
      this.data = this._temp.filter((e) => e.id == +event.target.value)
      this.collectionSize = this.data.length
    }
  }
  print(){
    var url= Config.toApiUrl('structure/'+this.user.idEntite)
    if(this.user) url+="?imp=giwu" 
    window.open(url, "_blank")  
  }
  
  searchStats() {
    this._temp = []
    this.data = []
    this.requeteService.filterStatByStructure('','').subscribe((res: any) => {
      this.spinner.hide();
      res.forEach((e) => { if (e.idParent == 0) { this.data.push(e) } })
      this._temp = this.data
      this.collectionSize = this.data.length
    })
  }



  resetStats() {
    this.init()
  }

}
