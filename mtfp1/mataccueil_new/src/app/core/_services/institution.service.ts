import { Injectable } from '@angular/core';
import { HttpClient , HttpHeaders } from '@angular/common/http';
import { Config } from '../../app.config';
import { tap } from 'rxjs/internal/operators/tap';
@Injectable({
  providedIn: 'root'
})
export class InstitutionService {

  constructor(private http:HttpClient) { }
 

  getAll(){
   
    return this.http.get<any[]>(`${Config.toApiUrl("institution")}`, Config.httpHeader(localStorage.getItem("mataccueilToken"),true));
  }

  get(id){
    return this.http.get<any>(`${Config.toApiUrl("institution/")}${id}`, Config.httpHeader(localStorage.getItem("mataccueilToken"),true)).pipe(
      tap((ressource: any) => console.log(`get ressource ${ressource}`))
    );
  }
  create(ressource){
    return this.http.post<any>(`${Config.toApiUrl("institution")}`, ressource,
     Config.httpHeader(localStorage.getItem("mataccueilToken"),true)).pipe(
      tap((ressource: any) => console.log(`added ressource ${ressource}`))
      );
    }
  update(ressource,id){
    return this.http.post<any>(`${Config.toApiUrl("institution/")}${id}`, ressource,
    Config.httpHeader(localStorage.getItem("mataccueilToken"),true)).pipe(
      tap((ressource: any) => console.log(`upadted ressource ${ressource}`))
      );
  }
  delete(id:number){
    return this.http.delete<any[]>(`${Config.toApiUrl("institution/")}${id}`,Config.httpHeader(localStorage.getItem("mataccueilToken"),false));
  }
  // -- Relance 

  getAll_Relance(id){
    return this.http.get<any[]>(`${Config.toApiUrl("relanceconfig")}/${id}`, Config.httpHeader(localStorage.getItem("mataccueilToken"),true));
  }
  getLisUsersParEntite(id){

    return this.http.get<any[]>(`${Config.toApiUrl("lisuserRelance")}/${id}`, Config.httpHeader(localStorage.getItem("mataccueilToken"),true));
  }
  createRelance(ressource){
    return this.http.post<any>(`${Config.toApiUrl("relanceconfig")}`, ressource,
     Config.httpHeader(localStorage.getItem("mataccueilToken"),true)).pipe(
      tap((ressource: any) => console.log(`added ressource ${ressource}`))
      );
    }
    
  getAllEntite(){
    return this.http.get<any[]>(`${Config.toApiUrl("allministere")}`, Config.httpHeader(localStorage.getItem("mataccueilToken"),true));
  }
  updateRelance(ressource,id){
    return this.http.post<any>(`${Config.toApiUrl("relanceconfig/")}${id}`, ressource,
    Config.httpHeader(localStorage.getItem("mataccueilToken"),true)).pipe(
      tap((ressource: any) => console.log(`upadted ressource ${ressource}`))
      );
  }
  deleteRelance(id:number){
    return this.http.delete<any[]>(`${Config.toApiUrl("relanceconfig/")}${id}`,Config.httpHeader(localStorage.getItem("mataccueilToken"),false));
  }
}
