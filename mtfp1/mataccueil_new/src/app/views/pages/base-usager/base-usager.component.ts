import { Component, OnInit } from '@angular/core';
import { CanActivate, ActivatedRouteSnapshot, RouterStateSnapshot, UrlTree, Router, ActivatedRoute } from '@angular/router';
import { Observable } from 'rxjs';
import { AuthentificationService } from '../../../core/_services/authentification.service';
import { LocalService } from '../../../core/_services/browser-storages/local.service';
import {JwtHelperService} from '@auth0/angular-jwt';

@Component({
  selector: 'app-base-usager',
  templateUrl: './base-usager.component.html',
  styleUrls: ['./base-usager.component.css']
})
export class BaseUsagerComponent implements OnInit {

  constructor(private activatedRoute: ActivatedRoute,private jwtHelper: JwtHelperService,private router: Router, private auth:AuthentificationService,private localStorageService:LocalService) { 

    
  }

  ngOnInit(): void {
  }

}

