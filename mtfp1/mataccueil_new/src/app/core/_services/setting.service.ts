import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { tap } from 'rxjs/operators';
import { Config } from 'src/app/app.config';

@Injectable({
  providedIn: 'root'
})
export class SettingService {

  constructor(private http:HttpClient) { }

  get(){
  
    return this.http.get<any[]>(`${Config.toApiUrl("settings")}`, Config.httpHeader(localStorage.getItem("mataccueilToken"),true));
  }

  create(ressource){
    console.log(ressource)

    return this.http.post<any>(`${Config.toApiUrl("settings")}`, ressource,
     Config.httpHeader(localStorage.getItem("mataccueilToken"),true)).pipe(
      tap((ressource: any) => console.log(`added ressource ${ressource}`))
    );
  }

  update(id,ressource){
    ressource['_method']="patch"
    return this.http.post<any>(`${Config.toApiUrl("settings")}/${id}`, ressource,
     Config.httpHeader(localStorage.getItem("mataccueilToken"),true)).pipe(
      tap((ressource: any) => console.log(`added ressource ${ressource}`))
    );
  }
}
