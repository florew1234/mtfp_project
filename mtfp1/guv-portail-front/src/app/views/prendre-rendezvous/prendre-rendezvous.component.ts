import { Component, OnInit, ViewChild } from '@angular/core';
import { PdaService } from '../../core/_services/pda.servic';
import { AlertNotif } from '../../alert';
import { Router } from '@angular/router';
import {NgbModal, ModalDismissReasons} from '@ng-bootstrap/ng-bootstrap';

@Component({
  selector: 'app-prendre-rendezvous',
  templateUrl: './prendre-rendezvous.component.html',
  styleUrls: ['./prendre-rendezvous.component.css']
})
export class PrendreRendezvousComponent implements OnInit {

  structures = []
  crenaux = []
  constructor(private pdaService: PdaService,private router:Router, private modalService:NgbModal) { }
  recup_data:any
  closeResult=""
  @ViewChild('content') content : any;

  open(content) {
    this.modalService.open(content, {ariaLabelledBy: 'modal-basic-title'}).result.then((result) => {
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

  ngOnInit(): void {
    window.scroll(0,0);
    this.structures = []
    this.pdaService.getStructures(1, 1).subscribe(
      (res: any) => {
        this.structures = res
      })

    this.crenaux = []
    this.pdaService.getRdvCreneaux().subscribe(
      (res: any) => {
        this.crenaux = res
      })
  }

  loading=false

  save(value) {
    this.recup_data=value
    this.modalService.open(this.content);
   
  }

  showCreneaux(id){
    let el=this.crenaux.filter(e=>(e.id==id))[0]
    return el.heureDebut+" "+el.heureFin
  }
  showStructure(id){
    let el=this.structures.filter(e=>(e.id==id))[0]
    return el.libelle
  }

  validate(){
    let value:any=this.recup_data  
    value.status=1
    value.idEntite=1
    this.loading=true
     this.pdaService.storeRDV(value).subscribe((res:any)=>{
      this.loading=false
      this.modalService.dismissAll()
      if(res.status=="success"){
        AlertNotif.finish("Rendez-vous","Demande de rendez-vous envoyée avec succès","success")
        this.router.navigateByUrl('/main')
      }else{
        AlertNotif.finish("Rendez-vous",res.msg,"error")
      }
     }, (err)=>{
      this.loading=false;
        AlertNotif.finish("Erreur","Une erreur est survenue lors du processus. Veuillez contacter l'administrateur ou réessayer plutard","error")}
      )
  }
}

