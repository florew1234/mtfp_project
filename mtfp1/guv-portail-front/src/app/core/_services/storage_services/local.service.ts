import { Injectable } from '@angular/core';
import {StorageService} from './storage.service';

@Injectable({
  providedIn: 'root'
})
export class LocalService {

  constructor(private storageService: StorageService) { }

  getItem(key: string) {
    return this.storageService.decrypt(localStorage.getItem(key));
  }
 
  setItem(key: string, item: any) {
    localStorage.setItem(key, this.storageService.encrypt(item));
  }
 // Get the json value from local storage
 getJsonValue(key: string) {
  return this.storageService.decrypt(localStorage.getItem(key)) ;
}
setJsonValue(key: string, value: any) {
  localStorage.setItem(key, this.storageService.encrypt(value));
}
  removeItem(key: string) {
    localStorage.removeItem(key);
  }

  clear() {
    localStorage.clear();
  }

}
