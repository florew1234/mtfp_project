import { Injectable } from '@angular/core';
import * as CryptoJS from 'crypto-js';
declare var require: any

const SECRET_KEY = 'berrabo_admin_project_key-dfa84rr8ge';

@Injectable({
  providedIn: 'root'
})
export class StorageService {

  constructor() { }

  // Encrypt the localstorage data
   encrypt(data) {
    data = CryptoJS.AES.encrypt(JSON.stringify(data), SECRET_KEY);
    data = data.toString();
    return data;
  }
  // Decrypt the encrypted data
  decrypt(data) {
    data = CryptoJS.AES.decrypt(data, SECRET_KEY);
    data =  JSON.parse(data.toString(CryptoJS.enc.Utf8));
    return data;
  }

}

