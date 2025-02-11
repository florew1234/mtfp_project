import { Injectable } from '@angular/core';
import { HttpClient , HttpHeaders } from '@angular/common/http';
import { Config } from '../../app.config';
import { tap } from 'rxjs/internal/operators/tap';
@Injectable({
  providedIn: 'root'
})
export class CommentaireService {

  constructor(private http:HttpClient) { }
 

  getAll(){
    //  ok
    return this.http.get<any[]>(`${Config.toApiUrl("comment")}`, Config.httpHeader(localStorage.getItem("mataccueilToken"),true));
  }
  get(id){
    return this.http.get<any>(`${Config.toApiUrl("etape/getprofil/")}${id}`, Config.httpHeader(localStorage.getItem("mataccueilToken"),true)).pipe(
      tap((ressource: any) => console.log(`get ressource ${ressource}`))
    );
  }
  create(ressource){
    return this.http.post<any>(`${Config.toApiUrl("comment")}`, ressource,
     Config.httpHeader(localStorage.getItem("mataccueilToken"),true)).pipe(
      tap((ressource: any) => console.log(`added ressource ${ressource}`))
    );
  }
  update(ressource,id){
    return this.http.post<any>(`${Config.toApiUrl("comment/")}${id}`, ressource,
     Config.httpHeader(localStorage.getItem("mataccueilToken"),true)).pipe(
      tap((ressource: any) => console.log(`upadted ressource ${ressource}`))
    );
  }
  delete(id:number){
    return this.http.delete<any[]>(`${Config.toApiUrl("comment/")}${id}`,Config.httpHeader(localStorage.getItem("mataccueilToken"),false));
  }
}
