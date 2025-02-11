import { Component, OnInit } from '@angular/core';
import { PdaService } from '../../core/_services/pda.servic';

@Component({
  selector: 'app-structures',
  templateUrl: './structures.component.html',
  styleUrls: ['./structures.component.css']
})
export class StructuresComponent implements OnInit {

  data=[]
  constructor(private pdaService:PdaService) { }

  ngOnInit(): void {
    window.scroll(0,0);
    this.data=[]
    this.pdaService.getStructures(0,1).subscribe(
      (res:any)=>{
              this.data=res
      },)
  }


}
