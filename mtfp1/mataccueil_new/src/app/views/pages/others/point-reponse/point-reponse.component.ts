import { Component, OnInit, Input } from '@angular/core';
import { PipeTransform } from '@angular/core';
import { DecimalPipe } from '@angular/common';
import { FormControl } from '@angular/forms';

import { Observable, Subject } from 'rxjs';
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
  selector: 'app-point-reponse',
  templateUrl: './point-reponse.component.html',
  styleUrls: ['./point-reponse.component.css']
})
export class PointReponseComponent implements OnInit {

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

   selected = [];
  current_permissions:any[]=[]
  selected_data:any
  isSended=false
  
  search(){ 
    this.data=[]
    this._temp=[]
    if(this.user.agent_user!=null && (this.user.profil_user.direction==1)){
    this.requeteService.getAllPointStructure(this.searchText,this.user.id,this.page,this.user.idEntite,this.user.agent_user.idStructure,1).subscribe((res:any)=>{
      this.spinner.hide();
      this.data=res.data
      this._temp=this.data
      this.subject.next(res);
    })
    }else{
      this.requeteService.getAllPoint(this.searchText,this.user.id,this.page,this.user.idEntite,1).subscribe((res:any)=>{
        this.spinner.hide();
        this.data=res.data
        this._temp=this.data
        this.subject.next(res);
      })
    }
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
  
  
  etapes=[]
  services=[]
  departements=[]
  structureservices=[]
  themes=[]
  natures=[]

  isGeneralDirector=false

  show_step(id){
    return this.etapes.find((e)=>(e.id==id))
  }

  ngOnInit(): void {

    
    if (localStorage.getItem('mataccueilUserData') != null) {
      this.user = this.localService.getJsonValue('mataccueilUserData')
      if(this.user.profil_user.CodeProfil === 12){
        this.isGeneralDirector=true;
      }else{
        this.isGeneralDirector=false;
      }
    }
    this.etapes=[]
    this.etapeService.getAll(this.user.idEntite).subscribe((res:any)=>{
      this.etapes=res
      this.activatedRoute.queryParams.subscribe(x => this.init(x.page || 1));
    })
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

    pager: any = {current_page: 0,
    data:[],
    last_page: 0,
    per_page: 0,
    to: 0,
    total: 0
  }
  subject = new Subject<any>();
  Null=null

  init(page){
   
    this._temp=[]
    this.data=[]
    if(this.user.agent_user!=null && (this.user.profil_user.direction==1)){
      this.requeteService.getAllPointStructure(null,this.user.id,page,this.user.idEntite,this.user.agent_user.idStructure,1).subscribe((res:any)=>{
        this.spinner.hide();
        this.data=res.data
        this._temp=this.data
        this.subject.next(res);
      })
    }else{
      this.requeteService.getAllPoint(null,this.user.id,page,this.user.idEntite,1).subscribe((res:any)=>{
        this.spinner.hide();
        this.data=res.data
        this._temp=this.data
        this.subject.next(res);
      })
    }

  
    this.departements=[]
    this.usagersService.getAllDepartement().subscribe((res:any)=>{
      this.departements=res
    })
    this.services=[]
    this.prestationService.getAll(this.user.idEntite).subscribe((res:any)=>{
      this.services=res
    })

    this.structureservices=[]
    this.structureService.getAllStructureByUser(this.user.id).subscribe((res:any)=>{
      this.structureservices=res
    })
    this.natures=[]
    this.natureService.getAll(this.user.idEntite).subscribe((res:any)=>{
      this.natures=res
    })
    this.themes=[]
    this.thematiqueService.getAll(this.user.idEntite).subscribe((res:any)=>{
      this.themes=res
    })

    
  }

  filter(ev:any){
    
  }

/*
  daysBetweenTwoDate (date2,date1){
    let timeDiff=0
    const date4 = new Date(date2);
    const date3 = new Date(date1);
    timeDiff = date3.getTime() - date4.getTime();
    let daysdiff=Math.ceil(timeDiff / (1000 * 3600 * 24))
    return daysdiff;
  }

  daysTodayFromDate(checkdate){
    let timeDiff=0
    const date = new Date(checkdate);
    timeDiff = (new Date()).getTime() - date.getTime();
    let daysdiff=Math.ceil(timeDiff / (1000 * 3600 * 24))
    return daysdiff;
  }
  ratioBetweenTwoDate(delaiTh,date2,date1){
    var date4 = new Date(date2);
    var date3 = new Date(date1);

    var timeDiff = Math.abs(date4.getTime() - date3.getTime());
    var dayDifference = Math.ceil(timeDiff / (1000 * 3600 * 24));
    var ratio = dayDifference/delaiTh;
    return ratio;
  }
  ratioTodayFromDate (delaiTh,date){
    var date2 = new Date();
    var date1 = new Date(date);
    var timeDiff = Math.abs(date2.getTime() - date1.getTime());
    var dayDifference = Math.ceil(timeDiff / (1000 * 3600 * 24));
    var ratio = dayDifference/delaiTh;

    return ratio;
  }

*/
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
