import { Injectable } from '@angular/core';
import { HttpClient , HttpHeaders } from '@angular/common/http';
import { Config } from '../../app.config';
import { tap } from 'rxjs/internal/operators/tap';
@Injectable({
  providedIn: 'root'
})
export class ServiceService {

  constructor(private http:HttpClient) { }
 

  getAll(idEntite){
   
    return this.http.get<any[]>(`${Config.toApiUrl("service")}/${idEntite}`, Config.httpHeader(localStorage.getItem("mataccueilToken"),true));
  }

  getAllEntite(){
    return this.http.get<any[]>(`${Config.toApiUrl("ministere")}`, Config.httpHeader(localStorage.getItem("mataccueilToken"),true));
  }
  getAllAttrib(idEntite){
   
    return this.http.get<any[]>(`${Config.toApiUrl("attri")}/${idEntite}`, Config.httpHeader(localStorage.getItem("mataccueilToken"),true));
  }
  getAllType(type){
   
    return this.http.get<any[]>(`${Config.toApiUrl("service/type")}/${type}`, Config.httpHeader(localStorage.getItem("mataccueilToken"),true));
  }
  getServPiece(idSer){
   
    return this.http.get<any[]>(`${Config.toApiUrl("servicePiece")}/${idSer}`, Config.httpHeader(localStorage.getItem("mataccueilToken"),true));
  }
  getAllStatByStrcutre(idEntite){
   
    return this.http.get<any[]>(`${Config.toApiUrl("statistiques/prestations-par-structure")}/${idEntite}`, Config.httpHeader(localStorage.getItem("mataccueilToken"),true));
  }
  getAllByStructure(idStructure){
   
    return this.http.get<any[]>(`${Config.toApiUrl("service/byStructure")}/${idStructure}`, Config.httpHeader(localStorage.getItem("mataccueilToken"),true));
  }
  getAllByCreator(){
   
    return this.http.get<any[]>(`${Config.toApiUrl("service/byCreator")}`, Config.httpHeader(localStorage.getItem("mataccueilToken"),true));
  }
  getStat(param,idEntite){
   
    return this.http.post<any[]>(`${Config.toApiUrl("statistiques/prestations")}/${idEntite}`,param, Config.httpHeader(localStorage.getItem("mataccueilToken"),true));
  }
  genPdfStat(param){
   
    return this.http.post<any[]>(`${Config.toApiUrl("genererpdfstat")}`,param, Config.httpHeader(localStorage.getItem("mataccueilToken"),true));
  }
  
  genPdfStatHebdo(param){
   
    return this.http.post<any[]>(`${Config.toApiUrl("genererpdfstathebdo")}`,param, Config.httpHeader(localStorage.getItem("mataccueilToken"),true));
  }
  
  
  

 

  get(id){
    return this.http.get<any>(`${Config.toApiUrl("service/getprofil/")}${id}`, Config.httpHeader(localStorage.getItem("mataccueilToken"),true)).pipe(
      tap((ressource: any) => console.log(`get ressource ${ressource}`))
    );
  }


  create(ressource){
    return this.http.post<any>(`${Config.toApiUrl("service")}`, ressource,
     Config.httpHeader(localStorage.getItem("mataccueilToken"),true)).pipe(
      tap((ressource: any) => console.log(`added ressource ${ressource}`))
    );
  }

  savePiece(ressource){
    return this.http.post<any>(`${Config.toApiUrl("service/savepiece")}`, ressource,
     Config.httpHeader(localStorage.getItem("mataccueilToken"),true)).pipe(
      tap((ressource: any) => console.log(`added ressource ${ressource}`))
    );
  }
  
  update(ressource,id){
    return this.http.post<any>(`${Config.toApiUrl("service/")}${id}`, ressource,
     Config.httpHeader(localStorage.getItem("mataccueilToken"),true)).pipe(
      tap((ressource: any) => console.log(`upadted ressource ${ressource}`))
    );
  }
  delete(id:number){
    return this.http.delete<any[]>(`${Config.toApiUrl("service/")}${id}`,Config.httpHeader(localStorage.getItem("mataccueilToken"),false));
  }
}
