import { Injectable } from '@angular/core';
import {Config} from '../../app.config';
import {HttpClient} from '@angular/common/http';
import {Observable} from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class StatusService {
    url = Config.toApiUrl('status');
    urlI = Config.toApiUrl('institution');

    constructor(private http: HttpClient) { }

    getAll(): Observable<any> {
        return this.http.get<any[]>(this.url, Config.httpHeader());
    }

    getAllStruc(): Observable<any> {
        return this.http.get<any[]>(this.urlI, Config.httpHeader());
    }

    getNbreServ(): Observable<any> {
        return this.http.get<any[]>(Config.toApiUrl('serviceEnLign'), Config.httpHeader());
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

    stats() {
        return this.http.get(`${Config.toApiUrl('stats')}`, Config.httpHeader());
    }

    

}
