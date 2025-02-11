import { Injectable } from '@angular/core';
import { HttpClient , HttpHeaders } from '@angular/common/http';
import { Config } from '../../app.config';
import { tap } from 'rxjs/internal/operators/tap';
@Injectable({
  providedIn: 'root'
})
export class DateRdvService {

  constructor(private http:HttpClient) { }
 

  getAll(idEntite){
   
    return this.http.get<any[]>(`${Config.toApiUrl("daterdv")}/${idEntite}`, Config.httpHeader(localStorage.getItem("mataccueilToken"),true));
  }
  getAllActif(idEntite){
   
    return this.http.get<any[]>(`${Config.toApiUrl("daterdv/actif")}/${idEntite}`, Config.httpHeader(localStorage.getItem("mataccueilToken"),true));
  }
  
  get(id){
    return this.http.get<any>(`${Config.toApiUrl("daterdv/getprofil/")}${id}`, Config.httpHeader(localStorage.getItem("mataccueilToken"),true)).pipe(
      tap((ressource: any) => console.log(`get ressource ${ressource}`))
    );
  }
  create(ressource){
    return this.http.post<any>(`${Config.toApiUrl("daterdv")}`, ressource,
     Config.httpHeader(localStorage.getItem("mataccueilToken"),true)).pipe(
      tap((ressource: any) => console.log(`added ressource ${ressource}`))
    );
  }
  update(ressource,id){
    return this.http.post<any>(`${Config.toApiUrl("daterdv/")}${id}`, ressource,
     Config.httpHeader(localStorage.getItem("mataccueilToken"),true)).pipe(
      tap((ressource: any) => console.log(`upadted ressource ${ressource}`))
    );
  }
  delete(id:number){
    return this.http.delete<any[]>(`${Config.toApiUrl("daterdv/")}${id}`,Config.httpHeader(localStorage.getItem("mataccueilToken"),false));
  }
}
