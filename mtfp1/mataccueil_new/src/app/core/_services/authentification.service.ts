import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Observable, of, Subject } from 'rxjs';
import { User } from '../_models/user.model';
import { Permission } from '../_models/permission.model';
import { catchError, map } from 'rxjs/operators';
import { Config } from '../../app.config';

@Injectable({
  providedIn: 'root'
})
export class AuthentificationService {

  url=Config.toApiUrl("auth");
  userLoggedInData = new Subject<any>();
  constructor(private http: HttpClient) {
  }

  GetPath(name) {
    return Config.toFile('public/Usager-mail/'+name);
  }
  setUserData(data) {
    this.userLoggedInData.next(data);
    console.log(data)
  }

  getUserLoggedInData(): Observable<any> {
    return this.userLoggedInData.asObservable();
  }

  isLoggedIn(){
    return true;
  }
  // Authentication/Authorization
  login(value) {

    return this.http.post(`${this.url}`, value,{headers:Config.httpHeader(null,false)});
  }
  loginUsager(value) {

    return this.http.post(`${Config.toApiUrl("authusager")}`, value,{headers:Config.httpHeader(null,false)});
  }
  
  
  getUserSinceGuv(key,value){
    const userToken = localStorage.getItem('auth/userdata');
     //return this.http.get(`http://localhost:8001/api/user/data?key=${key}&value=${value}`, Config.httpHeader(localStorage.getItem("mataccueilToken"),true));
    // return this.http.get(`http://api.guv.sevmtfp.test/api/user/data?key=${key}&value=${value}`, Config.httpHeader(localStorage.getItem("mataccueilToken"),true));
    return this.http.get(`https://back.guvmtfp.gouv.bj/api/user/data?key=${key}&value=${value}`, Config.httpHeader(localStorage.getItem("mataccueilToken"),true));
  }

  getUserByToken(){
    const userToken = localStorage.getItem('auth/userdata');
    return this.http.get(`${Config.toApiUrl('auth/userdata')}`, Config.httpHeader(localStorage.getItem("mataccueilToken"),true));
  }
  loginV2(code:any): Observable<any> {

    var formData=new FormData()
    formData.append("code",code)
    return this.http.post(`${Config.toApiUrl("auth-me")}`, formData,{headers:Config.httpHeader(null,false)});
  }

  register(user: User): Observable<any> {
    let httpHeaders = new HttpHeaders();
    httpHeaders = httpHeaders.set('Content-Type', 'application/json');
    return this.http.post<User>(`${this.url}`, user, {headers: httpHeaders})
      .pipe(
        map((res: User) => {
          return res;
        }),
        catchError(err => {
          return null;
        })
      );
  }

  /*
   * Submit forgot password request
   *
   * @param {string} email
   * @returns {Observable<any>}
   */
  public forgotPassword(email: string): Observable<any> {
    return this.http.post(`${Config.toApiUrl("password_reset/")}`,{email:email})
      .pipe(catchError(this.handleError('forgot-password', []))
      );
  }
  public resetPassword(password: string,token:string): Observable<any> {
    return this.http.post(`${Config.toApiUrl("password_reset/confirm/")}`,{password:password,token:token})
      .pipe(catchError(this.handleError('forgot-password', []))
      );
  }

   /*
   * Handle Http operation that failed.
   * Let the app continue.
    *
  * @param operation - name of the operation that failed
   * @param result - optional value to return as the observable result
   */
  private handleError<T>(operation = 'operation', result?: any) {
    return (error: any): Observable<any> => {
      // TODO: send the error to remote logging infrastructure
      console.error(error); // log to console instead

      // Let the app keep running by returning an empty result.
      return of(result);
    };
  }

}
