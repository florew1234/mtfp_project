import { Component, OnInit } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { AlertNotif } from '../../alert';
import { Router } from '@angular/router';

@Component({
  selector: 'app-info-carriere',
  templateUrl: './info-carriere.component.html',
  styleUrls: ['./info-carriere.component.css']
})
export class InfoCarriereComponent implements OnInit {

  constructor(private http: HttpClient,private router:Router) { }

  ngOnInit(): void {

  }


 
}
