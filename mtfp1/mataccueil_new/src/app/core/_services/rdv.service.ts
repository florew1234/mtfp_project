import { Injectable } from '@angular/core';
import { HttpClient , HttpHeaders } from '@angular/common/http';
import { Config } from '../../app.config';
import { tap } from 'rxjs/internal/operators/tap';
@Injectable({
  providedIn: 'root'
})
export class RdvService {

  constructor(private http:HttpClient) { }
 

  getAll(idEntite,seach,page){
    if(seach==null){
      return this.http.get<any[]>(`${Config.toApiUrl("rdv")}/${idEntite}?page=${page}`, Config.httpHeader(localStorage.getItem("mataccueilToken"),true));
    }else{
    return this.http.get<any[]>(`${Config.toApiUrl("rdv")}/${idEntite}?seach=${seach}&page=${page}`, Config.httpHeader(localStorage.getItem("mataccueilToken"),true));
    }
  }
  getAllByStructure(idStructure,page){
      return this.http.get<any[]>(`${Config.toApiUrl("rdv/byStructure")}/${idStructure}?page=${page}`, Config.httpHeader(localStorage.getItem("mataccueilToken"),true));
  }
  
  getAllForUsager(idUsager){
   
    return this.http.get<any[]>(`${Config.toApiUrl("rdv/usager")}/${idUsager}`, Config.httpHeader(localStorage.getItem("mataccueilToken"),true));
  }
  get(id){
    return this.http.get<any>(`${Config.toApiUrl("rdv/getprofil/")}${id}`, Config.httpHeader(localStorage.getItem("mataccueilToken"),true)).pipe(
      tap((ressource: any) => console.log(`get ressource ${ressource}`))
    );
  }
  create(ressource){
    return this.http.post<any>(`${Config.toApiUrl("rdv")}`, ressource,
     Config.httpHeader(localStorage.getItem("mataccueilToken"),true)).pipe(
      tap((ressource: any) => console.log(`added ressource ${ressource}`))
    );
  }
  saveRdvStatut(ressource){
    return this.http.post<any>(`${Config.toApiUrl("rdv/statut")}`, ressource,
     Config.httpHeader(localStorage.getItem("mataccueilToken"),true)).pipe(
      tap((ressource: any) => console.log(`added ressource ${ressource}`))
    );
  }
  
  update(ressource,id){
    return this.http.post<any>(`${Config.toApiUrl("rdv/")}${id}`, ressource,
     Config.httpHeader(localStorage.getItem("mataccueilToken"),true)).pipe(
      tap((ressource: any) => console.log(`upadted ressource ${ressource}`))
    );
  }
  delete(id:number){
    return this.http.delete<any[]>(`${Config.toApiUrl("rdv/")}${id}`,Config.httpHeader(localStorage.getItem("mataccueilToken"),false));
  }
}
