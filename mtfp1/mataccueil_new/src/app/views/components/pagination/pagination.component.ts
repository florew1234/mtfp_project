import { Component, OnInit, Input } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';

@Component({
  selector: 'app-pagination',
  templateUrl: './pagination.component.html',
  styleUrls: ['./pagination.component.css']
})
export class PaginationComponent implements OnInit {

  @Input()  pager = {current_page:0,last_page:0,pages:[]};
  @Input() path:string
  constructor(private router:Router) { }

  ngOnInit(): void {
    console.log(this.router.url)
    let pages=[]
    for (let index = 1; index < this.pager.last_page; index++) {
      pages.push(index)
    }
    this.pager.pages=pages
  }

}
