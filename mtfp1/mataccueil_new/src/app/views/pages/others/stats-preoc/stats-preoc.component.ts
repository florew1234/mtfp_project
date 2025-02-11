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
import { ActeurService } from '../../../../core/_services/acteur.service';
import { StructureService } from '../../../../core/_services/structure.service';
import { LocalService } from '../../../../core/_services/browser-storages/local.service';
import { Config } from '../../../../app.config';
import { NatureRequeteService } from '../../../../core/_services/nature-requete.service';
import { ThemeService } from 'ng2-charts';
import { TypeService } from '../../../../core/_services/type.service';
import { isDeepStrictEqual } from 'util';


@Component({
  selector: 'app-stats-preoc',
  templateUrl: './stats-preoc.component.html',
  styleUrls: ['./stats-preoc.component.css']
})
export class StatspreocComponent implements OnInit {


  @Input() cssClasses = '';
  errormessage = ""
  erroraffectation = ""

  searchText = ""
  closeResult = '';
  permissions: any[]
  error = ""
  data: any[] = [];
  data_stat: any;
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

  search(){ 
    this.statConnetivite=this._temp.filter(r => {
      const term = this.searchText.toLowerCase();
      return r.nomprenoms.toLowerCase().includes(term) ||
      (r.structure==null ? '' : r.structure.libelle).toString().toLowerCase().includes(term)
    })
    this.collectionSize=this.statConnetivite.length
  }

  list_parcours=[]

  // openEditModal(content,el){
  //   this.list_parcours=el
  //   this.modalService.open(content, {ariaLabelledBy: 'modal-basic-title'}).result.then((result) => {
  //     this.closeResult = `Closed with: ${result}`;
  //   }, (reason) => {
  //     this.closeResult = `Dismissed ${this.getDismissReason(reason)}`;
  //   });
  // }

  // private getDismissReason(reason: any): string {
  //   if (reason === ModalDismissReasons.ESC) {
  //     return 'by pressing ESC';
  //   } else if (reason === ModalDismissReasons.BACKDROP_CLICK) {
  //     return 'by clicking on a backdrop';
  //   } else {
  //     return `with: ${reason}`;
  //   }
  // }

  user: any

  constructor(
    private modalService: NgbModal,
    private userService: UserService,
    private router: Router,
    private translate: TranslateService,

    private acteurService: ActeurService,
    private requeteService: RequeteService,
    private localService: LocalService,
    private usagersService: UsagerService,
    private spinner: NgxSpinnerService,
    private activatedRoute: ActivatedRoute,
  ) { }


  etapes = []
  statEservice = []
  lastConn = []
  statConnetivite = []

  isAdmin = false
  serv_rej = ""
  serv_ntr = ""
  serv_susp = ""
  inter_face = ""

  ngOnInit(): void {
    
    this.prepare()
    this.router.events
      .subscribe(event => {
        if (event instanceof NavigationStart) {
          this.prepare()
        }
      })

    
    
  }

  nbreRelance = 0
  nbreInstance = 0
  openAddModal(content, iduser, idStru,idprofil) {
    let cpt = 0
    if(idStru != 0 && idStru != null && idprofil != null){ 

      if(idprofil == 23 ){// Directeur
        this.requeteService.getAllRequest_stat(1,iduser, idStru,this.user.id).subscribe((res: any) => {

          res.data.forEach((e) => { 
            if (e.relance.length != 0) { 
                e.relance.forEach((r) => { 
                  if (r.idStructure == idStru) { 
                    cpt++
                  } 
                })
             } 
          })

          this.nbreRelance = cpt
          this.data_stat = res.data.filter(e=>{
            if(e.lastparcours != null){
              return (e.lastparcours.idEtape==1) || 
                        (e.lastparcours.idEtape==5) || 
                        (e.lastparcours.idEtape==7 && e.lastparcours.idStructure == idStru) ||
                        (e.lastparcours.idEtape==8 && e.lastparcours.idEntite == 1);
            }else{
              return (e.lastparcours == null);
            }
          })
          this.nbreInstance = this.data_stat.length
        })
        // alert("Direction User : "+iduser+" Struc : "+idStru+" profil : "+idprofil+" nombre : "+this.data_stat.length) 
      }else if(idprofil == 24){// Chef Service
        this.requeteService.getAllRequest_stat(1,iduser, idStru,this.user.id).subscribe((res: any) => {
            res.data.forEach((e) => { 
              if (e.relance.length != 0) { 
                  e.relance.forEach((r) => { 
                    if (r.idStructure == idStru) { 
                      cpt++
                    } 
                  })
              } 
            })
            this.nbreRelance = cpt

            this.data_stat = res.data.filter(e=>{
              if(e.lastparcours != null){
                return (e.lastparcours.idEtape==1) ||
                        (e.lastparcours.idEtape==2 && e.lastparcours.idStructure == idStru) ||
                        (e.lastparcours.idEtape==4) ||
                        (e.lastparcours.idEtape==7 && e.lastparcours.idEntite == 1);
              }else{
                return (e.lastparcours == null);
              }
            })
            this.nbreInstance = this.data_stat.length
          })
      }else if(idprofil == 25){// Chef Division
        this.requeteService.getAllRequest_stat(1,iduser, idStru,this.user.id).subscribe((res: any) => {
          res.data.forEach((e) => { 
            if (e.relance.length != 0) { 
                e.relance.forEach((r) => { 
                  if (r.idStructure == idStru) { 
                    cpt++
                  } 
                })
            } 
          })
          this.nbreRelance = cpt

          this.data_stat = res.data.filter(e=>{
            if(e.lastparcours != null){
              return (e.lastparcours.idEtape==1) || 
                      (e.lastparcours.idEtape==3 && e.lastparcours.idStructure == idStru);
            }else{
              return (e.lastparcours == null);
            }
          })
          this.nbreInstance = this.data_stat.length
        })
      }else if(idprofil == 33){// Adjoint
        this.requeteService.getAllRequest_stat(1,iduser, idStru, this.user.id).subscribe((res: any) => {
          res.data.forEach((e) => { 
            if (e.relance.length != 0) { 
                e.relance.forEach((r) => { 
                  if (r.idStructure == idStru) { 
                    cpt++
                  } 
                })
            } 
          })
          this.nbreRelance = cpt

          this.data_stat = res.data
          this.nbreInstance = this.data_stat.length
        })
      }else{
        this.nbreRelance = 0
        this.nbreInstance = 0
      }
    }else{
      this.nbreRelance = 0
      this.nbreInstance = 0
    }
    this.modalService.open(content, { ariaLabelledBy: 'modal-basic-title'}).result.then((result) => {
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
  prepare() {
   
    if (localStorage.getItem('mataccueilUserData') != null) {
      this.user = this.localService.getJsonValue('mataccueilUserData')
      if (this.user.id == '74') { //Administrateur
        this.isAdmin = true;
      } else {
        this.isAdmin = false;
      }
      console.log(this.user)
      this.statEservice = []
      this.acteurService.Recup_Stat_E_Service("").subscribe((res:any)=>{
        this.statEservice = res.data.stats
      })

      this.statConnetivite = []
      this.acteurService.getAllConnection(1).subscribe((res:any)=>{
        this.statConnetivite = res
        this._temp = this.statConnetivite
        this.collectionSize=this.statConnetivite.length
        console.log("sssssssssssssssss")
        console.log(res)
      })

      // ntr - rej - susp
      if(this.activatedRoute.snapshot.paramMap.get('type_req') == 'serv'){
        this.inter_face = ""
        if(this.activatedRoute.snapshot.paramMap.get('col') == 'rej'){
            this.serv_rej = "ok"  
        }else if(this.activatedRoute.snapshot.paramMap.get('col') == 'ntr'){
          this.serv_ntr = "ok"  
        }else if(this.activatedRoute.snapshot.paramMap.get('col') == 'susp'){
          this.serv_susp = "ok"  
        }
      }
      // ntr - rej - susp
      if(this.activatedRoute.snapshot.paramMap.get('type_req') == 'conn'){
        this.inter_face = "conn"
      }

    }
    // alert('okok')
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


  }

  selected_idcom=""
  selected_iduse=""
  select_date_start=""
  select_date_end=""

  filter(value){
    
  }
  

}
