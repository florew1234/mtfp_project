import { Component, OnInit, Input } from '@angular/core';
import { PipeTransform } from '@angular/core';
import { DecimalPipe } from '@angular/common';
import { FormControl } from '@angular/forms';

import { Observable } from 'rxjs';
import { map, startWith } from 'rxjs/operators';
import {NgbModal, ModalDismissReasons} from '@ng-bootstrap/ng-bootstrap';
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
import { InstitutionService } from 'src/app/core/_services/institution.service';

@Component({
  selector: 'app-list-stat-prestation',
  templateUrl: './list-stat-prestation.component.html',
  styleUrls: ['./list-stat-prestation.component.css']
})
export class ListStatPrestationComponent implements OnInit {


  @Input() cssClasses = '';
  errormessage=""
  erroraffectation=""
  
  searchText=""
  closeResult = '';
  permissions:any[]
  error=""
  data: any[]=[];
  entities: any[]=[];
  _temp: any[]=[];
  collectionSize = 0;
  page = 1;
  pageSize = 10;
  entiteId=1;
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
    private insService:InstitutionService,
    private natureService:NatureRequeteService,
    private thematiqueService:TypeService,
    private usagersService:UsagerService,
    private spinner: NgxSpinnerService,
    private activatedRoute: ActivatedRoute,
  ) { }

  ngOnInit(): void {

    
    if (localStorage.getItem('mataccueilUserData') == null) {
      this.user = this.localService.getJsonValue('mataccueilUserData')
      this.init()
    }
   
  }

  init(){
    this._temp=[]
    this.data=[]
    let param={"idUser":this.user.id,"startDate":"all","endDate":"all"}
    this.entiteId=this.user.idEntite;
    this.prestationService.getStat(param,this.entiteId).subscribe((res:any)=>{
      this.spinner.hide();
      this.data=res.stats
      this.param_stat.stats=this.data
      this.param_stat.startDate="all"
      this.param_stat.endDate="all"
      this._temp=this.data
      this.collectionSize=this.data.length
    })

    this.insService.getAll().subscribe((res:any)=>{
      this.entities=res
    })

  }

  param_stat={startDate:"",endDate:"",idUser:'',stats:[]}

  searchStats(){
    this.param_stat.idUser=this.user.id
    this._temp=[]
    this.data=[]
    this.prestationService.getStat(this.param_stat,this.entiteId).subscribe((res:any)=>{
      this.spinner.hide();
      this.data=res.stats
      this.param_stat.stats=this.data
      this._temp=this.data
      this.collectionSize=this.data.length
    })
  }
  genererPDFStat(){
    this.param_stat.stats=this.data
    this.param_stat['entiteId']=this.entiteId
    this.prestationService.genPdfStat(this.param_stat).subscribe((res:any)=>{
      window.open(Config.toFile('statistiques/'+res.url),'_blank') 
    })
  }

  resetStats(){
    this.init()
    this.param_stat={startDate:"all",endDate:"all",idUser:'',stats:[]}
  }
}
