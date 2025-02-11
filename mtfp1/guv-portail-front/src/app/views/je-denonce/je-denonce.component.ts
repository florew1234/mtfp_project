import { Component, OnInit, ViewChild } from '@angular/core';
import { PdaService } from '../../core/_services/pda.servic';
import { AlertNotif } from '../../alert';
import { Router } from '@angular/router';
import {NgbModal, ModalDismissReasons} from '@ng-bootstrap/ng-bootstrap';


@Component({
  selector: 'app-je-denonce',
  templateUrl: './je-denonce.component.html',
  styleUrls: ['./je-denonce.component.css']
})
export class JeDenonceComponent implements OnInit {
  
  institutions = []
  constructor(private pdaService: PdaService, private router: Router,private modalService:NgbModal) { }

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
    window.scroll(0, 0);
    this.institutions = []
    this.pdaService.getEntiteFromIGSEP().subscribe(
      (res: any) => {
        this.institutions = res
      })
  }
  file: File
  select_entity=""
  loading = false

  showEntity(id){
    let el=this.institutions.filter(e=>(e.id==id))[0]
    return el.libelle
  }
  save(value) {
    this.loading = true
    value.resume+="\n Nom complet : "+value.nom+" "+value.prenoms
    if(this.select_entity!='private-admin'){
      if(this.select_entity!="" && this.select_entity!=null){
        value.resume+="\n Ministère / Institution de provenance : "+value.entity
      }
    }else{
      value.resume+="\n Administration privée de provenance : "+value.precision_entity
    }
    
    value.resume+="\n Téléphone : "+value.phone
    value.resume+="\n Email : "+value.email
    let formData = new FormData()
    formData.append("nom", value.nom)
    formData.append("prenoms", value.prenoms)
    formData.append("email", value.email)
    formData.append("phone", value.phone)
    formData.append("entity", value.entity)
    formData.append("precision_entity", value.precision_entity)
    formData.append("resume", value.resume)
    

    if (this.file != null) {
      formData.append("fichier_joint", this.file)
    }
    this.pdaService.storeDenonciation2(formData).subscribe((res: any) => {
      this.loading = false
      this.modalService.dismissAll()
      if (res.success) {
        AlertNotif.finish("Dénonciation", "Votre dénonciation a été envoyée avec succès", "success")
        this.router.navigateByUrl('/main')
      }
    }, (err)=>{
      this.loading=false;
        AlertNotif.finish("Erreur","Une erreur est survenue lors du processus. Veuillez contacter l'administrateur ou réessayer plutard","error")}
      )
  }
  onFileChange(event) {
    this.file = null
    if (event.target.files.length > 0) {
      this.file = event.target.files[0];
      //  this.form.get('avatar').setValue(file);
    }
  }
}
