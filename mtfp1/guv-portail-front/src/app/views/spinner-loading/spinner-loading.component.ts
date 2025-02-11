import {Component, Input, OnInit} from '@angular/core';

@Component({
  selector: 'app-spinner-loading',
  templateUrl: './spinner-loading.component.html',
  styleUrls: ['./spinner-loading.component.css']
})
export class SpinnerLoadingComponent implements OnInit {
    @Input() loading:boolean = false;

  constructor() { }

  ngOnInit(): void {
  }

}
