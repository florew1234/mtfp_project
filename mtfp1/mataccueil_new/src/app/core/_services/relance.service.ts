import { Injectable } from '@angular/core';
import { HttpClient , HttpHeaders } from '@angular/common/http';
import { Config } from '../../app.config';
import { tap } from 'rxjs/internal/operators/tap';
@Injectable({
  providedIn: 'root'
})
export class RelanceService {

  constructor(private http:HttpClient) { }
 

  getAll(idEntite){
   
    return this.http.get<any[]>(`${Config.toApiUrl("relance")}/${idEntite}`, Config.httpHeader(localStorage.getItem("mataccueilToken"),true));
  }
  
 
  create(ressource){
    return this.http.post<any>(`${Config.toApiUrl("relance")}`, ressource,
     Config.httpHeader(localStorage.getItem("mataccueilToken"),true)).pipe(
      tap((ressource: any) => console.log(`added ressource ${ressource}`))
    );
  }

}
