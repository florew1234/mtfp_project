import { Component, OnInit, Input } from '@angular/core';
import { PipeTransform } from '@angular/core';
import { DecimalPipe } from '@angular/common';
import { FormControl } from '@angular/forms';

import { Observable, Subject } from 'rxjs';
import { map, startWith } from 'rxjs/operators';
import {NgbModal, ModalDismissReasons} from '@ng-bootstrap/ng-bootstrap';
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
import { ThemeService, Label, Color } from 'ng2-charts';
import { TypeService } from '../../../../core/_services/type.service';
import { ChartDataSets } from 'chart.js';

@Component({
  selector: 'app-graphiqueevolution',
  templateUrl: './graphiqueevolution.component.html',
  styleUrls: ['./graphiqueevolution.component.css']
})
export class GraphiqueevolutionComponent implements OnInit {

  @Input() cssClasses = '';
  errormessage=""
  erroraffectation=""
  
  searchText=""
  closeResult = '';
  permissions:any[]
  error=""
  data: any[]=[];
  _temp: any[]=[];
  collectionSize = 0;
  page = 1;
  pageSize = 10;
  years=[]

  search(){ 
    this.data=this._temp.filter(r => {
      const term = this.searchText.toLowerCase();
      return r.question.toLowerCase().includes(term) 
    })
    this.collectionSize=this.data.length
  }
  

  user:any

  constructor(
    private modalService: NgbModal,
    private userService: UserService,
    private router:Router,
    private translate:TranslateService,
    private etapeService:EtapeService,
    private requeteService:RequeteService,
    private localService:LocalService,
    private prestationService:ServiceService,
    private structureService:StructureService,
    private natureService:NatureRequeteService,
    private thematiqueService:TypeService,
    private usagersService:UsagerService,
    private spinner: NgxSpinnerService,
    private activatedRoute: ActivatedRoute,
  ) { }

    lineChartData: ChartDataSets[] = [
    { data: [], label: 'Evolution des requetes' },
  ];
  
  linearData=[]
  lineChartLabels: Label[] = [];

  lineChartOptions = {
    responsive: true,
  };

  lineChartColors: Color[] = [
    {
      borderColor: 'black',
      backgroundColor: 'rgba(255,255,0,0.28)',
    },
  ];


  lineChartLegend = true;
  lineChartPlugins = [];
  lineChartType = 'line';
 

  selected_year=null

  typeRequete=""

  checkType(){
    if(this.activatedRoute.snapshot.paramMap.get('type_req')=="plaintes"){
      return {id:1,name:"plaintes"}
    }
    if(this.activatedRoute.snapshot.paramMap.get('type_req')=="requetes"){
      return {id:0,name:"requetes"}
    }
    if(this.activatedRoute.snapshot.paramMap.get('type_req')=="infos"){
      return {id:2,name:"demandes d'informations"}
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

     prepare(){
       for (let index = 2018; index <= 2040; index++) {
         this.years.push(index)
       }
   
       this.init()   
   
     
       this.activatedRoute.queryParams.subscribe(x => this.init());

      this.typeRequete = this.checkType().name;

      
       this.subject.subscribe((val) => {
         this.typeRequete = this.checkType().name;
         this.lineChartLabels=[]
         this.linearData=[]
         this.lineChartData=  [
          {
            data: [], label: ''
          },
  
        ]
         val.forEach(e=>{
          this.lineChartLabels.push(e.periode.toString())
          this.linearData.push(e.nbre)
        })
        this.lineChartData=[
          { data:  this.linearData, label: 
            'Evolution des '+this.checkType().name},
        ]
       })
     }
  init(){
    this._temp=[]
    this.data=[]
    this.lineChartLabels=[]
    this.linearData=[]
    this.lineChartData=  [
      {
        data: [], label: ''
      },

    ]
    this.requeteService.getGraphiqueStatEvolutionReq(
      this.checkType().id
      ,"all",this.user.idEntite).subscribe((res:any)=>{
      this.subject.next(res);
      /*res.forEach(e=>{
        this.lineChartLabels.push(e.periode.toString())
        this.linearData.push(e.nbre)
      })
      this.lineChartData=[
        { data:  this.linearData, label: 
          'Evolution des '+this.checkType().name},
      ]*/
    })

  }

  resetStats(){
    this.init()
    this.selected_year=null
  }

  loadGraphe(){
    if(this.selected_year!=null && this.selected_year!=""){
      this.requeteService.getGraphiqueStatEvolutionReq(
        this.checkType().id
        ,this.selected_year,this.user.idEntite).subscribe((res:any)=>{
        res.forEach(e=>{
          this.lineChartLabels.push(e.periode.toString())
          this.linearData.push(e.nbre)
        })
        this.lineChartData=[
          { data:  this.linearData, label: 
            'Evolution des '+this.checkType().name},
        ]
      })
    }
    
  }
}
