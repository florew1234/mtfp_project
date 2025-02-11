import { Injectable } from '@angular/core';
import { HttpClient , HttpHeaders } from '@angular/common/http';
import { Config } from '../../app.config';
import { tap } from 'rxjs/internal/operators/tap';
@Injectable({
  providedIn: 'root'
})
export class StructureService {

  constructor(private http:HttpClient) { }
 

  getAll(OnlyDirection,idEntite){

    return this.http.get<any[]>(`${Config.toApiUrl("structure")}/${OnlyDirection}/${idEntite}`, Config.httpHeader(localStorage.getItem("mataccueilToken"),true));
  }

  getStructureParThematique(idtype){

    return this.http.get<any[]>(`${Config.toApiUrl("structurethema")}/${idtype}`, Config.httpHeader(localStorage.getItem("mataccueilToken"),true));
  }

  getStructurePreocEnAttente(idEntite){

    return this.http.get<any[]>(`${Config.toApiUrl("structurePreocc")}/${idEntite}`, Config.httpHeader(localStorage.getItem("mataccueilToken"),true));
  }

  getPfc(){

    return this.http.get<any[]>(`${Config.toApiUrl("lispfc")}`, Config.httpHeader(localStorage.getItem("mataccueilToken"),true));
  }
  getLisCommune(id){

    return this.http.get<any[]>(`${Config.toApiUrl("liscommune")}/${id}`, Config.httpHeader(localStorage.getItem("mataccueilToken"),true));
  }
  getLisUsersParCommune(id){

    return this.http.get<any[]>(`${Config.toApiUrl("lisuser")}/${id}`, Config.httpHeader(localStorage.getItem("mataccueilToken"),true));
  }

  getAllStructureByUser(idUser){
   
    return this.http.get<any[]>(`${Config.toApiUrl("structure/get/sub")}/${idUser}`, Config.httpHeader(localStorage.getItem("mataccueilToken"),true));
  }
  get(id){
    return this.http.get<any>(`${Config.toApiUrl("structure/getLine/")}${id}`, Config.httpHeader(localStorage.getItem("mataccueilToken"),true)).pipe(
      tap((ressource: any) => console.log(`get ressource ${ressource}`))
    );
  }
  create(ressource){
    return this.http.post<any>(`${Config.toApiUrl("structure")}`, ressource,
     Config.httpHeader(localStorage.getItem("mataccueilToken"),true)).pipe(
      tap((ressource: any) => console.log(`added ressource ${ressource}`))
    );
  }
  update(ressource,id){
    return this.http.post<any>(`${Config.toApiUrl("structure/")}${id}`, ressource,
     Config.httpHeader(localStorage.getItem("mataccueilToken"),true)).pipe(
      tap((ressource: any) => console.log(`upadted ressource ${ressource}`))
    );
  }
  delete(id:number){
    return this.http.delete<any[]>(`${Config.toApiUrl("structure/")}${id}`,Config.httpHeader(localStorage.getItem("mataccueilToken"),false));
  }
}
