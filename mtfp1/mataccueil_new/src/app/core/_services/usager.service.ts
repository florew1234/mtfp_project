import { Injectable } from '@angular/core';
import { HttpClient , HttpHeaders } from '@angular/common/http';
import { Config } from '../../app.config';
import { tap } from 'rxjs/internal/operators/tap';
@Injectable({
  providedIn: 'root'
})
export class UsagerService {

  constructor(private http:HttpClient) { }
 

  getAll(search=null,page){
    if(search==null){
      return this.http.get<any[]>(`${Config.toApiUrl("usager")}?page=${page}`, Config.httpHeader(localStorage.getItem("mataccueilToken"),true));
    }else{
      return this.http.get<any[]>(`${Config.toApiUrl("usager")}?search=${search}&page=${page}`, Config.httpHeader(localStorage.getItem("mataccueilToken"),true));
      
    }
    
  }
  getAllSuggestion(){
    return this.http.get<any[]>(`${Config.toApiUrl("suggestion")}`, Config.httpHeader(localStorage.getItem("mataccueilToken"),true));
  }
  getAllDenonciation(){
    return this.http.get<any[]>(`${Config.toApiUrl("denonciation")}`, Config.httpHeader(localStorage.getItem("mataccueilToken"),true));
  }
  getAllDepartement(){
   
    return this.http.get<any[]>(`${Config.toApiUrl("departement")}`, Config.httpHeader(localStorage.getItem("mataccueilToken"),true));
  }
  get(id){
    return this.http.get<any>(`${Config.toApiUrl("usager/getprofil/")}${id}`, Config.httpHeader(localStorage.getItem("mataccueilToken"),true)).pipe(
      tap((ressource: any) => console.log(`get ressource ${ressource}`))
    );
  }
  create(ressource){
    return this.http.post<any>(`${Config.toApiUrl("usager")}`, ressource,
     Config.httpHeader(localStorage.getItem("mataccueilToken"),true)).pipe(
      tap((ressource: any) => console.log(`added ressource ${ressource}`))
    );
  }
  update(ressource,id){
    return this.http.post<any>(`${Config.toApiUrl("usager/")}${id}`, ressource,
     Config.httpHeader(localStorage.getItem("mataccueilToken"),true)).pipe(
      tap((ressource: any) => console.log(`upadted ressource ${ressource}`))
    );
  }
  
  delete(id:number){
    return this.http.delete<any[]>(`${Config.toApiUrl("usager/")}${id}`,Config.httpHeader(localStorage.getItem("mataccueilToken"),false));
  }
}
