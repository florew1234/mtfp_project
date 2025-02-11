import { Injectable } from '@angular/core';
import {Config} from '../../app.config';
import {HttpClient} from '@angular/common/http';
import {Observable} from 'rxjs';
import { environment } from '../../../environments/environment.prod';

@Injectable({
  providedIn: 'root'
})
export class PdaService {
    [x: string]: any;

    url = "http://localhost:8002/api";
    // url = "http://api.mataccueil.sevmtfp.test/api";
   //url_igsep="https://api.igsep.hebergeappli.bj/api"
    //url= "https://preprodmtfp.gouv.bj/pprod-mataccueilapi/api/"

    constructor(private http: HttpClient) { 
      
        
    }

    getFaqs(): Observable<any> {
        return this.http.get<any[]>(`${this.url}/faq`, Config.httpHeader());
    }


    getEvenementsDeclencheur(): Observable<any> {
        return this.http.get<any[]>(`${this.url}/evt/1`, Config.httpHeader());
    }
    getEntiteFromIGSEP(): Observable<any> {
        return this.http.get<any[]>(`${this.url}/institution`, Config.httpHeader());
    }
    getStructures(onlyDirection,entite): Observable<any> {
        return this.http.get<any[]>(`${this.url}/structure/${onlyDirection}/${entite}`, Config.httpHeader());
    }
    getStructures_new(entite): Observable<any> {
        return this.http.get<any[]>(`${this.url}/structure/${entite}`, Config.httpHeader());
    }
    getOneStructure(id): Observable<any> {
        return this.http.get<any[]>(`${this.url}/structure/getLine/${id}`, Config.httpHeader());
    }
    getThematiques(): Observable<any> {
        return this.http.get<any[]>(`${this.url}/type/1`, Config.httpHeader());
    }
    getOneThematique(id): Observable<any> {
        return this.http.get<any[]>(`${this.url}/type/getLine/${id}`, Config.httpHeader());
    }
    getPrestationsByThematique(type_id): Observable<any> {
        return this.http.get<any[]>(`${this.url}/service/type/${type_id}`, Config.httpHeader());
    }
    getPrestations(): Observable<any> {
        return this.http.get<any[]>(`${this.url}/service/1`, Config.httpHeader());
    }
    getPrestationsByStructure(structure_id): Observable<any> {
        return this.http.get<any[]>(`${this.url}/service/structure/${structure_id}`, Config.httpHeader());
    }
    getRdvCreneaux(): Observable<any> {
        return this.http.get<any[]>(`${this.url}/rdvcreneau/1`, Config.httpHeader());
    }
    getStat(param,idEntite){
   
        return this.http.post<any[]>(`${this.url}/statistiques/prestations/${idEntite}`,param, Config.httpHeader(localStorage.getItem("mataccueilToken"),true));
      }

    storeRDV(ressource: object) {
        return this.http.post(`${this.url}/usagers/externe/rdv_new`, ressource, Config.httpHeader());
    }
    storePreoccupation(ressource: object) {
        return this.http.post(`${this.url}/usagers/externe/requete_new`, ressource, Config.httpHeader());
    }
    verifyRecaptcha(token) {
        return this.http.get(`https://www.google.com/recaptcha/api/siteverify?secret=${environment.recaptcha.secret}&response=${token}`, Config.httpHeader());
    }
    storeDenonciation(ressource: object) {
        return this.http.post(`/requeteusager`, ressource, Config.httpHeader());
    }
    storeDenonciation2(ressource: object) {
        return this.http.post(`${this.url}/usagers/externe/je_denonce`, ressource, Config.httpHeader());
    }
    
    storeQuestion(ressource: object) {
        return this.http.post(`${this.url}/question`, ressource, Config.httpHeader());
    }
    store(ressource: object) {
        return this.http.post(this.url, ressource, Config.httpHeader());
    }
    show(id: number) {
        return this.http.get(`${this.url}/${id}`, Config.httpHeader());
    }
    update(ressource: object, id: number) {
        return this.http.put(`${this.url}/${id}`, ressource, Config.httpHeader());
    }
    delete(id: number) {
        return this.http.delete(`${this.url}/${id}`, Config.httpHeader());
    }
    state(id: number) {
        return this.http.get(`${this.url}/${id}/status`, Config.httpHeader());
    }

       
}
