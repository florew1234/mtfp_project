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
import { ThemeService, Label, Color, MultiDataSet } from 'ng2-charts';
import { TypeService } from '../../../../core/_services/type.service';
import { ChartDataSets, ChartOptions, ChartType } from 'chart.js';

@Component({
  selector: 'app-graphiquestructure',
  templateUrl: './graphiquestructure.component.html',
  styleUrls: ['./graphiquestructure.component.css']
})
export class GraphiquestructureComponent implements OnInit {

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
  years = []
  typeGraphe="Histogramme"

  search() {
    this.data = this._temp.filter(r => {
      const term = this.searchText.toLowerCase();
      return r.question.toLowerCase().includes(term)
    })
    this.collectionSize = this.data.length
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


  barChartOptions: ChartOptions = {
    responsive: true,
    scales: { xAxes: [{}], yAxes: [{}] },
  };
  barChartLabels: Label[] = [];
  barChartType: ChartType = 'horizontalBar';
  barChartLegend = true;
  barChartPlugins = [];

  barChartData: ChartDataSets[] = [
    { data: [], label: '' }
  ];
  barData = []
  selected_year = null


  doughnutChartLabels: Label[] = [];
  doughnutChartData: MultiDataSet = [
    []
  ];

  doughnutChartType: ChartType = 'doughnut';
  typeRequete=""

  checkType(){
    if(this.activatedRoute.snapshot.paramMap.get('type_req')=="plaintes"){
      return {id:1,name:"Plaintes"}
    }
    if(this.activatedRoute.snapshot.paramMap.get('type_req')=="requetes"){
      return {id:0,name:"Requetes"}
    }
    if(this.activatedRoute.snapshot.paramMap.get('type_req')=="infos"){
      return {id:2,name:"Demandes d'informations"}
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
  selected_type = ""

  prepare(){
    for (let index = 2018; index <= 2040; index++) {
      this.years.push(index)
    }

    this.init()   

    this.activatedRoute.queryParams.subscribe(x => this.init());

    // this.typeRequete = this.checkType().name;
    if(this.selected_type =="0"){
      this.typeRequete = 'de requêtes'
    }else if(this.selected_type =="1"){
      this.typeRequete = 'de plaintes'
    }else if(this.selected_type =="2"){
      this.typeRequete = "de demandes d'informations"
    }else {
      this.typeRequete = "de tous les types"
    }
    
    this.subject.subscribe((val) => {
      // this.typeRequete = this.checkType().name;
      this.barChartLabels = []
      this.doughnutChartLabels = []
      this.barData=[]
      this.barChartData = [
        {
          data: [], label: ''
        },

      ]

      val.forEach(e => {
        this.barChartLabels.push(e.libelle)
        this.doughnutChartLabels.push(e.libelle)
        this.barData.push(e.total)
      })
      this.barChartData = [
        {
          data: this.barData, label:
          'Nombre '+this.typeRequete+' par structure'
        },

      ]
      this.doughnutChartData=[this.barData]   
    })
  }

  init() {
    this.barChartLabels = []
    this.doughnutChartLabels = []
    this.barData=[]
    this.barChartData = [
      {
        data: [], label: ''
      },

    ]
    if(this.selected_type == ""){
      this.selected_type = "0"
    }
    this.requeteService.getAllGraphiqueStatStructure(
      this.selected_type,this.user.idEntite
    ).subscribe((res: any) => {
      this.subject.next(res);
      /*res.forEach(e => {
        this.barChartLabels.push(e.libelle)
        this.doughnutChartLabels.push(e.libelle)
        this.barData.push(e.total)
      })
      this.barChartData = [
        {
          data: this.barData, label:
          'Nombre  de '+this.checkType().name+' par thématique'
        },

      ]
      this.doughnutChartData=[this.barData]*/   
    })


  }

  param_stat={"user":"all","structure":"all","plainte":this.selected_type,startDate:"",endDate:""}


  searchStats(){

    if(this.selected_type == ""){
      this.selected_type = "0"
    }

    this._temp=[]
    this.data=[]
    this.param_stat.plainte = this.selected_type
    this.param_stat.structure = 'all'
    this.requeteService.filterAllGraphiqueStatStructure(
      this.param_stat,this.user.idEntite
    ).subscribe((res:any)=>{
      this.spinner.hide();
      this.data=res
      this._temp=this.data
      this.collectionSize=this.data.length

      this.subject.next(res);
          // --------
    })
  }
 

  resetStats(){
    this.init()
    this.selected_year = null

    this.param_stat={"user":"all","structure":"all","plainte":this.selected_type,startDate:"",endDate:""}
  }


}
