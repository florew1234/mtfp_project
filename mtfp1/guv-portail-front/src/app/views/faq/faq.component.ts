import { Component, OnInit } from '@angular/core';
import { PdaService } from '../../core/_services/pda.servic';

@Component({
  selector: 'app-faq',
  templateUrl: './faq.component.html',
  styleUrls: ['./faq.component.css']
})
export class FaqComponent implements OnInit {

  data=[]
  constructor(private pdaService:PdaService) { }

  ngOnInit(): void {
    window.scroll(0,0);
    this.data=[]
    this.pdaService.getFaqs().subscribe(
      (res:any)=>{
              this.data=res
      },)
  }

}
