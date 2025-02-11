import { Component, OnInit } from '@angular/core';
import { PdaService } from '../../core/_services/pda.servic';

@Component({
  selector: 'app-thematiques',
  templateUrl: './thematiques.component.html',
  styleUrls: ['./thematiques.component.css']
})
export class ThematiquesComponent implements OnInit {

  data=[]
  constructor(private pdaService:PdaService) { }

  ngOnInit(): void {
    window.scroll(0,0);
    this.data=[]
    this.pdaService.getThematiques().subscribe(
      (res:any)=>{
              this.data=res
      },)
  }


}
