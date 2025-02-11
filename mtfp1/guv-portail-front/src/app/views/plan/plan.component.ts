import { Component, OnInit } from '@angular/core';

@Component({
  selector: 'app-plan',
  templateUrl: './plan.component.html',
  styleUrls: ['./plan.component.css']
})
export class PlanComponent implements OnInit {

  constructor() { }

  ngOnInit(): void {
    window.scroll(0,0);
  }
  gotoHashtag(fragment: string) {
     
    setTimeout(function(){
      const element:any = document.querySelector("#" + fragment);
      if (element) element.scrollIntoView();
    })
}

}
