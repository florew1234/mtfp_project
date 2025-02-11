import { Injectable } from '@angular/core';
import * as CryptoJS from 'crypto-js';

const SECRET_KEY = 'GUV-MTFP@2020';

@Injectable({
  providedIn: 'root'
})
export class StorageService {

  constructor() { }

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
