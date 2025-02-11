import { Component, OnInit } from '@angular/core';
import { PdaService } from '../../core/_services/pda.servic';

@Component({
  selector: 'app-evenement-declencheur',
  templateUrl: './evenement-declencheur.component.html',
  styleUrls: ['./evenement-declencheur.component.css']
})
export class EvenementDeclencheurComponent implements OnInit {

  data=[]
  constructor(private pdaService:PdaService) { }

  ngOnInit(): void {
    window.scroll(0,0);
    this.data=[]
    this.pdaService.getEvenementsDeclencheur().subscribe(
      (res:any)=>{
              this.data=res
      },)
  }

}
