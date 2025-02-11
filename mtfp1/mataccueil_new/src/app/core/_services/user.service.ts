import { Injectable } from '@angular/core';
import { HttpClient , HttpHeaders } from '@angular/common/http';
import { Config } from '../../app.config';
import { catchError, tap, map } from 'rxjs/operators';
import { Roles } from '../_models/roles';
 


@Injectable({
  providedIn: 'root'
})
export class UserService {

  
  url=Config.toApiUrl("utilisateur");
  url_act=Config.toApiUrl("acteurcom");
  constructor(private http:HttpClient) { }
 

  getAllMain(){
   
    return this.http.get<any[]>(`${Config.toApiUrl("utilisateurs/all/main")}`, Config.httpHeader(localStorage.getItem("mataccueilToken"),true));
  }
  getAll(idEntite){
   
    return this.http.get<any[]>(`${this.url}/${idEntite}`, Config.httpHeader(localStorage.getItem("mataccueilToken"),true));
  }
  getAllActeur(idEntite){
    return this.http.get<any[]>(`${this.url_act}/${idEntite}`, Config.httpHeader(localStorage.getItem("mataccueilToken"),true));
  }

  get(id){
   
    return this.http.get<any[]>(`${this.url}/${id}/`, Config.httpHeader(localStorage.getItem("mataccueilToken"),true));
  }

  update_last_logout(id){
    return this.http.get<any[]>(`${Config.toApiUrl("user_last_logout")}/${id}`, Config.httpHeader(localStorage.getItem("mataccueilToken"),true));
  }
 

  create(ressource){
    return this.http.post<any>(Config.toApiUrl("utilisateur"), ressource,
     Config.httpHeader(localStorage.getItem("mataccueilToken"),true)).pipe(
      tap((ressource: any) => console.log(`added ressource ${ressource}`))
    );
  }

  soumettreSuggest(ressource){
    return this.http.post<any>(Config.toApiUrl("requetecomment/transmettre"), ressource,
     Config.httpHeader(localStorage.getItem("mataccueilToken"),true)).pipe(
      tap((ressource: any) => console.log(`added ressource ${ressource}`))
    );
  }

  
  update(ressource,id){
    return this.http.post<any>(`${this.url}/${id}`, ressource,  Config.httpHeader(localStorage.getItem("mataccueilToken"),true)).pipe(
      tap((ressource: any) => console.log(`updated ressource ${ressource}`))
    );
  }
  updateProfil(ressource){
    return this.http.post<any>(`${Config.toApiUrl("utilisateur/profil/update")}`, ressource,  Config.httpHeader(localStorage.getItem("mataccueilToken"),true)).pipe(
      tap((ressource: any) => console.log(`updated ressource ${ressource}`))
    );
  }

  

  set_password(ressource,id){
    return this.http.patch<any>(`${this.url}${id}/set_password/`, ressource,  Config.httpHeader(localStorage.getItem("mataccueilToken"),true)).pipe(
      tap((ressource: any) => console.log(`updated ressource ${ressource}`))
    );
  }
  
  delete(id:number){
    return this.http.delete<any[]>(`${this.url}/${id}`, Config.httpHeader(localStorage.getItem("mataccueilToken"),true));
  }

 
  change_password(value){
    return this.http.put<any[]>(`${this.url}/change/password`,value,Config.httpHeader());
  }

  setState(id:any,state:any){
    return this.http.get<any[]>(`${Config.toApiUrl("users-set-state")}/${id}/state/${state}`,Config.httpHeader(localStorage.getItem("mataccueilToken"),true));
  }
  
}
