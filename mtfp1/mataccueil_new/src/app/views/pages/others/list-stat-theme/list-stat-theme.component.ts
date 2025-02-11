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
import { ThemeService } from 'ng2-charts';
import { TypeService } from '../../../../core/_services/type.service';


@Component({
  selector: 'app-list-stat-theme',
  templateUrl: './list-stat-theme.component.html',
  styleUrls: ['./list-stat-theme.component.css']
})
export class ListStatThemeComponent implements OnInit {

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

  selected_type=""

  search(){ 
    this.data=this._temp.filter(r => {
      const term = this.searchText.toLowerCase();
      return r.libelle.toLowerCase().includes(term) 
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

  prepare(){
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
      this.data=[]
      // this.typeRequete = this.checkType().name;
      this.data=val
    })
  }
  init(){
    if(this.selected_type == ""){
      this.selected_type = "0"
    }
    // this.typeRequete=this.checkType().name;
    this._temp=[]
    this.data=[]
    // this.requeteService.getStatByTheme(this.checkType().id,this.user.idEntite).subscribe((res:any)=>{
    this.requeteService.getStatByTheme(this.selected_type,this.user.idEntite).subscribe((res:any)=>{
      this.spinner.hide();
      this.data=res
      this.subject.next(res);
      this._temp=this.data
      this.collectionSize=this.data.length
    })

  }


  param_stat={"type":"all","plainte":this.selected_type,startDate:"",endDate:""}
  // param_stat={"type":"all","plainte":this.checkType().id,startDate:"",endDate:""}

  
  searchStats(){
    this._temp=[]
    this.data=[]
    this.param_stat.plainte = this.selected_type
    this.requeteService.filterStatByTheme(
      this.param_stat,this.user.idEntite
    ).subscribe((res:any)=>{
      this.spinner.hide();
      this.data=res
      this._temp=this.data
      this.collectionSize=this.data.length
    })
  }
 

  resetStats(){
    this.init()
    this.param_stat={"type":"all","plainte":this.selected_type,startDate:"",endDate:""}
  }

}
