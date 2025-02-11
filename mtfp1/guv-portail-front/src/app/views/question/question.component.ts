import { Component, OnInit } from '@angular/core';
import { PdaService } from '../../core/_services/pda.servic';
import { AlertNotif } from '../../alert';
import { Router } from '@angular/router';

@Component({
  selector: 'app-question',
  templateUrl: './question.component.html',
  styleUrls: ['./question.component.css']
})
export class QuestionComponent implements OnInit {

  constructor(private pdaService:PdaService,private router:Router) { }

  ngOnInit(): void {
    window.scroll(0,0);
  }


  loading=false
  save(value){
  
   this.loading=true
    
     this.pdaService.storeQuestion(value).subscribe((res:any)=>{
      this.loading=false
      if(res.success){
        AlertNotif.finish("Question","Votre question a été envoyée avec succès","success")
        this.router.navigateByUrl('/main')
      }
     }, (err)=>{
      this.loading=false;
        AlertNotif.finish("Erreur","Une erreur est survenue lors du processus. Veuillez contacter l'administrateur ou réessayer plutard","error")}
      )
  }
}
