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

@Component({
  selector: 'app-list-ratio-demande-infos-prestation',
  templateUrl: './list-ratio-demande-infos-prestation.component.html',
  styleUrls: ['./list-ratio-demande-infos-prestation.component.css']
})
export class ListRatioDemandeInfosPrestationComponent implements OnInit {

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

  data2: any[]=[];
  _temp2: any[]=[];
  collectionSize2 = 0;
  page2 = 1;
  pageSize2 = 10;

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



  ngOnInit(): void {

    
    if (localStorage.getItem('mataccueilUserData') != null) {
      this.user = this.localService.getJsonValue('mataccueilUserData')
      this.init()
    }
  
  }

  init(){
    this._temp=[]
    this.data=[]
    this.requeteService.getRationInfosPrestation({"startDate":"all","endDate":"all"},this.user.idEntite).subscribe((res:any)=>{
      this.spinner.hide();
      this.data=res
      this._temp=this.data
      this.collectionSize=this.data.length
    })


    this._temp2=[]
    this.data2=[]
    this.requeteService.getRationInfosPrestationEncours({"startDate":"all","endDate":"all"},this.user.idEntite).subscribe((res:any)=>{
      this.spinner.hide();
      this.data2=res
      this._temp2=this.data2
      this.collectionSize2=this.data2.length
    })

  }


}
