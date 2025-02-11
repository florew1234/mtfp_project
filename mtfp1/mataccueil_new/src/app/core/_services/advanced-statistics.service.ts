import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Config } from 'src/app/app.config';

@Injectable({
  providedIn: 'root'
})
export class AdvancedStatisticsService {
  constructor(private http:HttpClient) { }
 

  getTogetherViews(){
   
    return this.http.get<any[]>(`${Config.toApiUrl("get-together-views")}`, Config.httpHeader(localStorage.getItem("mataccueilToken"),true));
  }

  getTogetherViews2(){
   
    return this.http.get<any[]>(`${Config.toApiUrl("get-together-views2")}`, Config.httpHeader(localStorage.getItem("mataccueilToken"),true));
  }
  getPerformances(){
   
    return this.http.get<any[]>(`${Config.toApiUrl("get-performances")}`, Config.httpHeader(localStorage.getItem("mataccueilToken"),true));
  }
  
  getPerformancesVisits(){
   
    return this.http.get<any[]>(`${Config.toApiUrl("get-performances-visists")}`, Config.httpHeader(localStorage.getItem("mataccueilToken"),true));
  }
  
  printView(resource:any){
   
    return this.http.post<any[]>(`${Config.toApiUrl("print-view")}`,resource, Config.httpHeader(localStorage.getItem("mataccueilToken"),true));
  }
  
}
