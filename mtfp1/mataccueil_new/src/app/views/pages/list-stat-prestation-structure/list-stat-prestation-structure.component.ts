import { Component, OnInit, Input } from '@angular/core';
import { NgbModal } from '@ng-bootstrap/ng-bootstrap';
import { UserService } from '../../../core/_services/user.service';
import { Router, ActivatedRoute, NavigationStart } from '@angular/router';
import { LocalService } from '../../../core/_services/browser-storages/local.service';
import { ServiceService } from '../../../core/_services/service.service';
import { NgxSpinnerService } from 'ngx-spinner';

@Component({
  selector: 'app-list-stat-prestation-structure',
  templateUrl: './list-stat-prestation-structure.component.html',
  styleUrls: ['./list-stat-prestation-structure.component.css']
})
export class ListStatPrestationStructureComponent implements OnInit {

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



  user:any

  constructor(
    private modalService: NgbModal,
    private userService: UserService,
    private router:Router,
    private localService:LocalService,
    private prestationService:ServiceService,
    private spinner: NgxSpinnerService,
    private activatedRoute: ActivatedRoute,
  ) { }


  search(){ 
    this.data=this._temp.filter(r => {
      const term = this.searchText.toLowerCase();
      return r.libelle.toLowerCase().includes(term) 
    })
    this.collectionSize=this.data.length
  }
  ngOnInit(): void {

    if (localStorage.getItem('mataccueilUserData') != null) {
      this.user = this.localService.getJsonValue('mataccueilUserData')
      this.prepare()
     
    }
    

    
  }
  prepare(){
    this.init()    
  
  }

  init(){
    this._temp=[]
    this.data=[]
    this.prestationService.getAllStatByStrcutre(
      this.user.idEntite
    ).subscribe((res:any)=>{
      this.data=res
      this._temp=this.data
      this.collectionSize=this.data.length

    })

   
  }

}
