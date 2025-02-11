import { Component, OnInit, ViewChild } from '@angular/core';
import { PdaService } from '../../core/_services/pda.servic';
import { AlertNotif } from '../../alert';
import { Router } from '@angular/router';
//import { ReCaptchaV3Service } from 'ng-recaptcha';
import {NgbModal, ModalDismissReasons} from '@ng-bootstrap/ng-bootstrap';


@Component({
  selector: 'app-reclammation',
  templateUrl: './reclammation.component.html',
  styleUrls: ['./reclammation.component.css']
})
export class ReclammationComponent implements OnInit {
//private recaptchaV3Service: ReCaptchaV3Service
  constructor(private pdaService: PdaService, private router: Router,private modalService: NgbModal) { }

  closeResult = '';
  prestations = []
  recup_data:any
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
    window.scroll(0, 0);
    this.pdaService.getPrestations().subscribe(
      (res: any) => {
        this.prestations = res;

      })
  }

  loading = false

  save(value) {
    this.recup_data=value
    this.modalService.open(this.content);
    /*this.recaptchaV3Service.execute('submit_form')
      .subscribe((token: string) => {
        //console.debug(`Token [${token}] generated`);

        this.pdaService.verifyRecaptcha(token)
          .subscribe((verify: any) => {
            if (verify.success == true && verify.score >= 0.5 && verify.action == 'submit_form') {
              //show resume form
             
            }
          })
      });*/
  }

  showPrestation(id){
    return this.prestations.filter(e=>(e.id==id))[0].libelle
  }
  validate(){
    let value:any=this.recup_data
    value.plateforme = "PDA"
    value.idEntite = 1
    value.interfaceRequete = "Reclamation"
    value.link_to_prestation = 1
    value.plainte = 0
    this.loading = true
    this.pdaService.storePreoccupation(value).subscribe((res: any) => {
      this.loading = false
      this.modalService.dismissAll()
      if (res.success) {
        AlertNotif.finish("Demande de reclamation", "Reclamation envoyée avec succès", "success")
        this.router.navigateByUrl('/main')
      }
    }, (err) => {
      this.loading = false;
      AlertNotif.finish("Erreur", "Une erreur est survenue lors du processus. Veuillez contacter l'administrateur ou réessayer plutard", "error")
    })
  }
}
