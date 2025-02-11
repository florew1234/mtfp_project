import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { tap } from 'rxjs/operators';
import { Config } from 'src/app/app.config';

@Injectable({
  providedIn: 'root'
})
export class TypeStructureService {
  constructor(private http:HttpClient) { }
 

  getAll(){
   
    return this.http.get<any[]>(`${Config.toApiUrl("type-structures")}`, Config.httpHeader(localStorage.getItem("mataccueilToken"),true));
  }
  create(ressource){
    return this.http.post<any>(`${Config.toApiUrl("type-structures")}`, ressource,
     Config.httpHeader(localStorage.getItem("mataccueilToken"),true)).pipe(
      tap((ressource: any) => console.log(`added ressource ${ressource}`))
    );
  }
  update(ressource,id){
    ressource['_method']="patch"
    return this.http.post<any>(`${Config.toApiUrl("type-structures/")}${id}`, ressource,
     Config.httpHeader(localStorage.getItem("mataccueilToken"),true)).pipe(
      tap((ressource: any) => console.log(`upadted ressource ${ressource}`))
    );
  }
  delete(id:number){
    return this.http.delete<any[]>(`${Config.toApiUrl("type-structures/")}${id}`,Config.httpHeader(localStorage.getItem("mataccueilToken"),false));
  }

  setState(id:number, state:any){
    return this.http.get<any[]>(`${Config.toApiUrl("type-structures/")}${id}/state/${state}`,Config.httpHeader(localStorage.getItem("mataccueilToken"),false));
  }
}
