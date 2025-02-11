import { Component, OnInit } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { AlertNotif } from '../../alert';
import { Router } from '@angular/router';

@Component({
  selector: 'app-info-pension',
  templateUrl: './info-pension.component.html',
  styleUrls: ['./info-pension.component.css']
})
export class InfoPensionComponent implements OnInit {

  constructor(private http: HttpClient,private router:Router) { }


  ngOnInit(): void {
  }



}
