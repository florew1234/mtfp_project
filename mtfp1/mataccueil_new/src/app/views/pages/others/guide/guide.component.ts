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
import { StructureService } from '../../../../core/_services/structure.service';
import { ActeurService } from '../../../../core/_services/acteur.service';
import { Acteur } from '../../../../core/_models/acteur.model';
import { Config } from '../../../../app.config';
import { LocalService } from '../../../../core/_services/browser-storages/local.service';
import { UsagerService } from '../../../../core/_services/usager.service';
import { ProfilService } from '../../../../core/_services/profil.service';

@Component({
  selector: 'app-guide',
  templateUrl: './guide.component.html',
  styleUrls: ['./guide.component.css']
})
export class GuideComponent implements OnInit {

  
  @Input() cssClasses = '';
  page = 1;
  pageSize = 10;
  searchText=""
  closeResult = '';
  permissions:any[]
  error=""
  data: any[]=[];
  _temp: any[]=[];

  selected = [
  ];
  current_permissions:any[]=[]
  collectionSize = 0;
  selected_data:any


  

  constructor(
    private modalService: NgbModal,
    private userService: UserService,
    private router:Router,
    private structureService:StructureService,
    private suggestionService:UsagerService,
    private translate:TranslateService,
    private spinner: NgxSpinnerService,
    private activatedRoute: ActivatedRoute,
    private localStorageService:LocalService,
    private profilService:ProfilService
    ) {}

    // structures:[]=[]

    user:any
    ngOnInit() {
      if (localStorage.getItem('mataccueilUserData') != null) {
        this.user = this.localStorageService.getJsonValue("mataccueilUserData")
        
      }
     this.init()
    }
  init(){

    this._temp=[]
    this.data=[]
    this.profilService.get(this.user.idprofil).subscribe((res:any)=>{
      this.spinner.hide();
      this.data=res 
    })

  }
  
 



}
