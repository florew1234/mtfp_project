import { Injectable } from '@angular/core';
import { HttpClient , HttpHeaders } from '@angular/common/http';
import { Config } from '../../app.config';
import { tap } from 'rxjs/internal/operators/tap';
@Injectable({
  providedIn: 'root'
})
export class ProfilService {

  constructor(private http:HttpClient) { }
 

  getAllMain(){
   
    return this.http.get<any[]>(`${Config.toApiUrl("profil")}/main`, Config.httpHeader(localStorage.getItem("mataccueilToken"),true));
  }

  getAll(){
   
    return this.http.get<any[]>(`${Config.toApiUrl("profil")}`, Config.httpHeader(localStorage.getItem("mataccueilToken"),true));
  }
  get(id){
    return this.http.get<any>(`${Config.toApiUrl("profil/getprofil/")}${id}`, Config.httpHeader(localStorage.getItem("mataccueilToken"),true)).pipe(
      tap((ressource: any) => console.log(`get ressource ${ressource}`))
    );
  }
  create(ressource){
    
    return this.http.post<any>(`${Config.toApiUrl("profil")}`, ressource,
     Config.httpHeader(localStorage.getItem("mataccueilToken"),true)).pipe(
      tap((ressource: any) => console.log(`added ressource ${ressource}`))
    );
  }
  update(ressource,id){
    return this.http.post<any>(`${Config.toApiUrl("profil/")}${id}`, ressource,
     Config.httpHeader(localStorage.getItem("mataccueilToken"),true)).pipe(
      tap((ressource: any) => console.log(`upadted ressource ${ressource}`))
    );
  }
  delete(id:number){
    return this.http.delete<any[]>(`${Config.toApiUrl("profil/")}${id}`,Config.httpHeader(localStorage.getItem("mataccueilToken"),false));
  }  

  addGuideUser(param,id){
    return this.http.post<any[]>(`${Config.toApiUrl("profilGuide")}/${id}`,param, Config.httpHeader(localStorage.getItem("mataccueilToken"),true));
  }
}
