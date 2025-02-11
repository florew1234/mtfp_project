import { Injectable } from '@angular/core';
import { HttpClient , HttpHeaders } from '@angular/common/http';
import { Config } from '../../app.config';
import { tap } from 'rxjs/internal/operators/tap';
@Injectable({
  providedIn: 'root'
})
export class RequeteService {

  constructor(private http:HttpClient) { }
 
  getGraphiqueStatEvolutionReq(plainte,year="all",idEntite){
    return this.http.get<any[]>(`${Config.toApiUrl("statistiques/")}year/${plainte}/${year}/${idEntite}`, Config.httpHeader(localStorage.getItem("mataccueilToken"),true));
  }
  getAllGraphiqueStatStructure(plainte,idEntite){
    return this.http.get<any[]>(`${Config.toApiUrl("stats/nbre/all/")}${plainte}/${idEntite}`, Config.httpHeader(localStorage.getItem("mataccueilToken"),true));
  }
  filterAllGraphiqueStatStructure(param,idEntite){
    return this.http.post<any[]>(`${Config.toApiUrl("stats/nbre")}/${idEntite}`,param, Config.httpHeader(localStorage.getItem("mataccueilToken"),true));
  }

  
  getAllGraphiqueStatSTheme(plainte,idEntite){
    return this.http.get<any[]>(`${Config.toApiUrl("statistiques/type/all/")}${plainte}/${idEntite}`, Config.httpHeader(localStorage.getItem("mataccueilToken"),true));
  }
  filterAllGraphiqueStatSTheme(param,idEntite){
    return this.http.post<any[]>(`${Config.toApiUrl("statistiques/type")}/${idEntite}`,param, Config.httpHeader(localStorage.getItem("mataccueilToken"),true));
  }

  getAllRequest(idEntite,search,traiteOuiNon,idUser,structure,plainte,page){
    // ok &typeStructure=${type}
    if(structure == ""){
      if(search==null){
       return this.http.get<any[]>(`${Config.toApiUrl("requeteusager/get")}/${idEntite}?traiteOuiNon=${traiteOuiNon}&idUser=${idUser}&page=${page}`, Config.httpHeader(localStorage.getItem("mataccueilToken"),true));
      }else{
       return this.http.get<any[]>(`${Config.toApiUrl("requeteusager/get")}/${idEntite}?traiteOuiNon=${traiteOuiNon}&idUser=${idUser}&search=${search}&page=${page}`, Config.httpHeader(localStorage.getItem("mataccueilToken"),true));
      }
    }else{
      if(search==null){
       return this.http.get<any[]>(`${Config.toApiUrl("requeteusager/get")}/${idEntite}?traiteOuiNon=${traiteOuiNon}&idUser=${idUser}&structure=${structure}&plainte=${plainte}&page=${page}`, Config.httpHeader(localStorage.getItem("mataccueilToken"),true));
      }else{
       return this.http.get<any[]>(`${Config.toApiUrl("requeteusager/get")}/${idEntite}?traiteOuiNon=${traiteOuiNon}&idUser=${idUser}&structure=${structure}&plainte=${plainte}&search=${search}&page=${page}`, Config.httpHeader(localStorage.getItem("mataccueilToken"),true));
      }
    }

  }

  getAllRequest_stat(idEntite,idUser,structure,id_connUse){
    // ok &typeStructure=${type} plainte
    return this.http.get<any[]>(`${Config.toApiUrl("requeteusager/get_stat")}/${idEntite}?idUser=${idUser}&structure=${structure}&id_connUse=${id_connUse}`, Config.httpHeader(localStorage.getItem("mataccueilToken"),true));
  }

  getStatByTheme(plainte,idEntite){
    return this.http.get<any[]>(`${Config.toApiUrl("statistiques/get/stat/all")}/${plainte}/${idEntite}`, Config.httpHeader(localStorage.getItem("mataccueilToken"),true));
  }

  filterStatByTheme(param,idEntite){
    return this.http.post<any[]>(`${Config.toApiUrl("statistiques/get/stat")}/${idEntite}`,param, Config.httpHeader(localStorage.getItem("mataccueilToken"),true));
  }

  
  getStatByStructure(plainte,idEntite){
    return this.http.get<any[]>(`${Config.toApiUrl("statistiques/nbre/all")}/${plainte}/${idEntite}`, Config.httpHeader(localStorage.getItem("mataccueilToken"),true));
  }
  
  getAll_Structure(idEntite){
    return this.http.get<any[]>(`${Config.toApiUrl("structure")}/${idEntite}`, Config.httpHeader(localStorage.getItem("mataccueilToken"),true));
  }
  getStatAllStructure(plainte,idEntite){
    return this.http.get<any[]>(`${Config.toApiUrl("statistiques/all-strucuture")}/${plainte}/${idEntite}`, Config.httpHeader(localStorage.getItem("mataccueilToken"),true));
  }
  

  filterStatByStructure(param,idEntite){
    return this.http.post<any[]>(`${Config.toApiUrl("statistiques/nbre")}/${idEntite}`,param, Config.httpHeader(localStorage.getItem("mataccueilToken"),true));
  }
  

  getRationReqPrestationEncours(param,idEntite){
    return this.http.post<any[]>(`${Config.toApiUrl("ratio/requeteprestationsencours")}/${idEntite}`,param, Config.httpHeader(localStorage.getItem("mataccueilToken"),true));
  }
  getRationReqPrestation(param,idEntite){
    return this.http.post<any[]>(`${Config.toApiUrl("ratio/requeteprestations")}/${idEntite}`,param, Config.httpHeader(localStorage.getItem("mataccueilToken"),true));
  }
  getRationInfosPrestationEncours(param,idEntite){
    return this.http.post<any[]>(`${Config.toApiUrl("ratio/demandesinfosprestationsencours")}/${idEntite}`,param, Config.httpHeader(localStorage.getItem("mataccueilToken"),true));
  }
  getRationInfosPrestation(param,idEntite){
    return this.http.post<any[]>(`${Config.toApiUrl("ratio/demandesinfosprestations")}/${idEntite}`,param, Config.httpHeader(localStorage.getItem("mataccueilToken"),true));
  }
  getRationPlaintePrestationEncours(param,idEntite){
    return this.http.post<any[]>(`${Config.toApiUrl("ratio/plainteprestationsencours")}/${idEntite}`,param, Config.httpHeader(localStorage.getItem("mataccueilToken"),true));
  }
  getRationPlaintePrestation(param,idEntite){
    return this.http.post<any[]>(`${Config.toApiUrl("ratio/plainteprestations")}/${idEntite}`,param, Config.httpHeader(localStorage.getItem("mataccueilToken"),true));
  }

  getRationReqStructureEncours(param,idEntite){
    return this.http.post<any[]>(`${Config.toApiUrl("ratio/requeteservicesencours")}/${idEntite}`,param, Config.httpHeader(localStorage.getItem("mataccueilToken"),true));
  }
  getRationReqStructure(param,idEntite){
    return this.http.post<any[]>(`${Config.toApiUrl("ratio/requeteservices")}/${idEntite}`,param, Config.httpHeader(localStorage.getItem("mataccueilToken"),true));
  }
  getRationInfosStructureEncours(param,idEntite){
    return this.http.post<any[]>(`${Config.toApiUrl("ratio/demandesinfosservicesencours")}/${idEntite}`,param, Config.httpHeader(localStorage.getItem("mataccueilToken"),true));
  }
  getRationInfosStructure(param,idEntite){
    return this.http.post<any[]>(`${Config.toApiUrl("ratio/demandesinfosservices")}/${idEntite}`,param, Config.httpHeader(localStorage.getItem("mataccueilToken"),true));
  }
  getRationPlainteStructureEncours(param,idEntite){
    return this.http.post<any[]>(`${Config.toApiUrl("ratio/plainteservicesencours")}/${idEntite}`,param, Config.httpHeader(localStorage.getItem("mataccueilToken"),true));
  }
  getRationPlainteStructure(param,idEntite){
    return this.http.post<any[]>(`${Config.toApiUrl("ratio/plainteservices")}/${idEntite}`,param, Config.httpHeader(localStorage.getItem("mataccueilToken"),true));
  }


  

  
  getStat(idUser,plainte,idEntite){
    return this.http.get<any[]>(`${Config.toApiUrl("statistiques/nbre")}/${idUser}/${plainte}/${idEntite}`, Config.httpHeader(localStorage.getItem("mataccueilToken"),true));
  }
  
  getStatCour(idUser,plainte,idEntite){
    return this.http.get<any[]>(`${Config.toApiUrl("statistiques/nbreCour")}/${idUser}/${plainte}/${idEntite}`, Config.httpHeader(localStorage.getItem("mataccueilToken"),true));
  }

  getAllPointReponse(search=null,idUser,page,idEntite){
   if(search==null){
    return this.http.get<any[]>(`${Config.toApiUrl("requeteusager/get")}/${idEntite}?idUser=${idUser}&page=${page}`, Config.httpHeader(localStorage.getItem("mataccueilToken"),true));
   }else{
    return this.http.get<any[]>(`${Config.toApiUrl("requeteusager/get")}/${idEntite}?idUser=${idUser}&search=${search}&page=${page}`, Config.httpHeader(localStorage.getItem("mataccueilToken"),true));
   }
  }
  getAllPoint(search=null,idUser,page,idEntite,traiteOuiNon){
    if(search==null){
     return this.http.get<any[]>(`${Config.toApiUrl("requeteusager/get")}/${idEntite}?idUser=${idUser}&page=${page}&traiteOuiNon=${traiteOuiNon}`, Config.httpHeader(localStorage.getItem("mataccueilToken"),true));
    }else{

     return this.http.get<any[]>(`${Config.toApiUrl("requeteusager/get")}/${idEntite}?idUser=${idUser}&search=${search}&page=${page}&traiteOuiNon=${traiteOuiNon}`, Config.httpHeader(localStorage.getItem("mataccueilToken"),true));
    }
   }
   getAllPointStructure(search=null,idUser,page,idEntite,structure,traiteOuiNon){
    if(search==null){
     return this.http.get<any[]>(`${Config.toApiUrl("requeteusager/get")}/${idEntite}?idUser=${idUser}&page=${page}&traiteOuiNon=${traiteOuiNon}&structure=${structure}`, Config.httpHeader(localStorage.getItem("mataccueilToken"),true));
    }else{

     return this.http.get<any[]>(`${Config.toApiUrl("requeteusager/get")}/${idEntite}?idUser=${idUser}&search=${search}&page=${page}&traiteOuiNon=${traiteOuiNon}&structure=${structure}`, Config.httpHeader(localStorage.getItem("mataccueilToken"),true));
    }
   } 
  getAllParcours(idEntite,search,idUser,plainte,page,idStructure,statut,startDate,endDate,type){
   if(search==null){
    return this.http.get<any[]>(`${Config.toApiUrl("requeteusager/get")}/${idEntite}?idUser=${idUser}&plainte=${plainte}&structure=${idStructure}&page=${page}&parc=oui&type=${type}`, Config.httpHeader(localStorage.getItem("mataccueilToken"),true));
   }else{
      if(idStructure!=null && statut!=null){
        if(startDate==null){
          return this.http.get<any[]>(`${Config.toApiUrl("requeteusager/get")}/${idEntite}?idUser=${idUser}&plainte=${plainte}&search=${search}&structure=${idStructure}&traiteOuiNon=${statut}&page=${page}&parc=oui&type=${type}`, Config.httpHeader(localStorage.getItem("mataccueilToken"),true));
        }else{
          return this.http.get<any[]>(`${Config.toApiUrl("requeteusager/get")}/${idEntite}?idUser=${idUser}&plainte=${plainte}&search=${search}&structure=${idStructure}&traiteOuiNon=${statut}&page=${page}&parc=oui&startDate=${startDate}&endDate=${endDate}&type=${type}`, Config.httpHeader(localStorage.getItem("mataccueilToken"),true));
        }
      }else if(idStructure==null && statut!=null){
      if(startDate==null){
        return this.http.get<any[]>(`${Config.toApiUrl("requeteusager/get")}/${idEntite}?idUser=${idUser}&plainte=${plainte}&search=${search}&traiteOuiNon=${statut}&page=${page}&parc=oui&type=${type}`, Config.httpHeader(localStorage.getItem("mataccueilToken"),true));
      }else{
        return this.http.get<any[]>(`${Config.toApiUrl("requeteusager/get")}/${idEntite}?idUser=${idUser}&plainte=${plainte}&search=${search}&traiteOuiNon=${statut}&page=${page}&parc=oui&startDate=${startDate}&endDate=${endDate}&type=${type}`, Config.httpHeader(localStorage.getItem("mataccueilToken"),true));
      }
     }else if(idStructure!=null && statut==null){
      if(startDate==null){
        return this.http.get<any[]>(`${Config.toApiUrl("requeteusager/get")}/${idEntite}?idUser=${idUser}&plainte=${plainte}&search=${search}&structure=${idStructure}&page=${page}&parc=oui&type=${type}`, Config.httpHeader(localStorage.getItem("mataccueilToken"),true));       
      }else{
        return this.http.get<any[]>(`${Config.toApiUrl("requeteusager/get")}/${idEntite}?idUser=${idUser}&plainte=${plainte}&search=${search}&structure=${idStructure}&page=${page}&parc=oui&startDate=${startDate}&endDate=${endDate}&type=${type}`, Config.httpHeader(localStorage.getItem("mataccueilToken"),true));       
      }
     }else{
        if(startDate==null){
          return this.http.get<any[]>(`${Config.toApiUrl("requeteusager/get")}/${idEntite}?idUser=${idUser}&plainte=${plainte}&search=${search}&page=${page}&parc=oui&type=${type}`, Config.httpHeader(localStorage.getItem("mataccueilToken"),true));
        }else{
          return this.http.get<any[]>(`${Config.toApiUrl("requeteusager/get")}/${idEntite}?idUser=${idUser}&plainte=${plainte}&search=${search}&page=${page}&parc=oui&startDate=${startDate}&endDate=${endDate}&type=${type}`, Config.httpHeader(localStorage.getItem("mataccueilToken"),true));
        }
     }
   }
  }
  getParcoursRegistre(idEntite,search,idcomm,page,statut,startDate,endDate,iduserCom){
   if(search==null){
    if(startDate==null){
      return this.http.get<any[]>(`${Config.toApiUrl("registreusager/get")}/${idEntite}?communue=${idcomm}&page=${page}&statut=${statut}&iduserCom=${iduserCom}`, Config.httpHeader(localStorage.getItem("mataccueilToken"),true));
    }else{
      return this.http.get<any[]>(`${Config.toApiUrl("registreusager/get")}/${idEntite}?communue=${idcomm}&page=${page}&startDate=${startDate}&endDate=${endDate}&statut=${statut}&iduserCom=${iduserCom}`, Config.httpHeader(localStorage.getItem("mataccueilToken"),true));
    }
   }else{
      if(startDate==null){
        return this.http.get<any[]>(`${Config.toApiUrl("registreusager/get")}/${idEntite}?communue=${idcomm}&search=${search}&page=${page}&statut=${statut}&iduserCom=${iduserCom}`, Config.httpHeader(localStorage.getItem("mataccueilToken"),true));
      }else{
        return this.http.get<any[]>(`${Config.toApiUrl("registreusager/get")}/${idEntite}?communue=${idcomm}&search=${search}&page=${page}&startDate=${startDate}&endDate=${endDate}&statut=${statut}&iduserCom=${iduserCom}`, Config.httpHeader(localStorage.getItem("mataccueilToken"),true));
      }
   }
  }
  getInfosPrint(idEntite,search,idUser,plainte,page,idStructure,statut,startDate,endDate){
   if(search==null){
    return this.http.get<any[]>(`${Config.toApiUrl("requeteusager/get")}/${idEntite}?idUser=${idUser}&plainte=${plainte}&structure=${idStructure}&page=${page}`, Config.httpHeader(localStorage.getItem("mataccueilToken"),true));
   }else{
      if(idStructure!=null && statut!=null){
        if(startDate==null){
          return this.http.get<any[]>(`${Config.toApiUrl("requeteusager/get")}/${idEntite}?idUser=${idUser}&plainte=${plainte}&search=${search}&structure=${idStructure}&traiteOuiNon=${statut}&page=${page}`, Config.httpHeader(localStorage.getItem("mataccueilToken"),true));
        }else{
          return this.http.get<any[]>(`${Config.toApiUrl("requeteusager/get")}/${idEntite}?idUser=${idUser}&plainte=${plainte}&search=${search}&structure=${idStructure}&traiteOuiNon=${statut}&page=${page}&startDate=${startDate}&endDate=${endDate}`, Config.httpHeader(localStorage.getItem("mataccueilToken"),true));
        }
      }else if(idStructure==null && statut!=null){
      if(startDate==null){
        return this.http.get<any[]>(`${Config.toApiUrl("requeteusager/get")}/${idEntite}?idUser=${idUser}&plainte=${plainte}&search=${search}&traiteOuiNon=${statut}&page=${page}`, Config.httpHeader(localStorage.getItem("mataccueilToken"),true));
      }else{
        return this.http.get<any[]>(`${Config.toApiUrl("requeteusager/get")}/${idEntite}?idUser=${idUser}&plainte=${plainte}&search=${search}&traiteOuiNon=${statut}&page=${page}&startDate=${startDate}&endDate=${endDate}`, Config.httpHeader(localStorage.getItem("mataccueilToken"),true));
      }
     }else if(idStructure!=null && statut==null){
      if(startDate==null){
        return this.http.get<any[]>(`${Config.toApiUrl("requeteusager/get")}/${idEntite}?idUser=${idUser}&plainte=${plainte}&search=${search}&structure=${idStructure}&page=${page}`, Config.httpHeader(localStorage.getItem("mataccueilToken"),true));       
      }else{
        return this.http.get<any[]>(`${Config.toApiUrl("requeteusager/get")}/${idEntite}?idUser=${idUser}&plainte=${plainte}&search=${search}&structure=${idStructure}&page=${page}&startDate=${startDate}&endDate=${endDate}`, Config.httpHeader(localStorage.getItem("mataccueilToken"),true));       
      }
     }else{
        if(startDate==null){
          return this.http.get<any[]>(`${Config.toApiUrl("requeteusager/get")}/${idEntite}?idUser=${idUser}&plainte=${plainte}&search=${search}&page=${page}`, Config.httpHeader(localStorage.getItem("mataccueilToken"),true));
        }else{
          return this.http.get<any[]>(`${Config.toApiUrl("requeteusager/get")}/${idEntite}?idUser=${idUser}&plainte=${plainte}&search=${search}&page=${page}&startDate=${startDate}&endDate=${endDate}`, Config.httpHeader(localStorage.getItem("mataccueilToken"),true));
        }
     }
   }
  }
  getAll(idEntite,search,plainte,page){
   if(search==null){
    return this.http.get<any[]>(`${Config.toApiUrl("requeteusager")}/${idEntite}?plainte=${plainte}&page=${page}`, Config.httpHeader(localStorage.getItem("mataccueilToken"),true));
   }else{
    return this.http.get<any[]>(`${Config.toApiUrl("requeteusager")}/${idEntite}?plainte=${plainte}&search=${search}&page=${page}`, Config.httpHeader(localStorage.getItem("mataccueilToken"),true));
   }
  }
  getAllForUser(idEntite,search,byUser,idUser,page){
    if(search==null){
     return this.http.get<any[]>(`${Config.toApiUrl("requeteusager/get")}/${idEntite}?byUser=${byUser}&idUser=${idUser}&page=${page}`, Config.httpHeader(localStorage.getItem("mataccueilToken"),true));
    }else{
     return this.http.get<any[]>(`${Config.toApiUrl("requeteusager/get")}/${idEntite}?byUser=${byUser}&idUser=${idUser}&search=${search}&page=${page}`, Config.httpHeader(localStorage.getItem("mataccueilToken"),true));
    }
   }
  getAllForUsager(idUsager,page){
   
    return this.http.get<any[]>(`${Config.toApiUrl("requeteusager/getrequetebyusager")}/${idUsager}?page=${page}`, Config.httpHeader(localStorage.getItem("mataccueilToken"),true));
  }
  getAllForUsagerNT(idUsager,page){
    return this.http.get<any[]>(`${Config.toApiUrl("requeteusager/getrequetebyusagerNT")}/${idUsager}?page=${page}`, Config.httpHeader(localStorage.getItem("mataccueilToken"),true));
  }
  
  getAllAffectation(idUser,typeStructure,plainte,page){
   
    return this.http.get<any[]>(`${Config.toApiUrl("affectation/get")}?idUser=${idUser}&typeStructure=${typeStructure}&plainte=${plainte}&page=${page}`, Config.httpHeader(localStorage.getItem("mataccueilToken"),true));
  }
  createAffectation(ressource){
    return this.http.post<any>(`${Config.toApiUrl("affectation")}`, ressource,
     Config.httpHeader(localStorage.getItem("mataccueilToken"),true)).pipe(
      tap((ressource: any) => console.log(`added ressource ${ressource}`))
    );
  }
  saveReponse(ressource){
    return this.http.post<any>(`${Config.toApiUrl("requeteusager/savereponse")}`, ressource,
     Config.httpHeader(localStorage.getItem("mataccueilToken"),true)).pipe(
      tap((ressource: any) => console.log(`added ressource ${ressource}`))
    );
  }

  archiverReque(ressource){
    return this.http.post<any>(`${Config.toApiUrl("requeteusager/archivereque")}`, ressource,
     Config.httpHeader(localStorage.getItem("mataccueilToken"),true)).pipe(
      tap((ressource: any) => console.log(`added ressource ${ressource}`))
    );
  }

  ModifierReque(ressource){
    return this.http.post<any>(`${Config.toApiUrl("requeteusager/modifierReque")}`, ressource,
     Config.httpHeader(localStorage.getItem("mataccueilToken"),true)).pipe(
      tap((ressource: any) => console.log(`added ressource ${ressource}`))
    );
  }
  noterRequete(ressource){
    return this.http.post<any>(`${Config.toApiUrl("noter")}`, ressource,
     Config.httpHeader(localStorage.getItem("mataccueilToken"),true)).pipe(
      tap((ressource: any) => console.log(`added ressource ${ressource}`))
    );
  }
  transmettreRequeteExterne(ressource){
    return this.http.post<any>(`${Config.toApiUrl("requeteusager/transmettre/externe")}`, ressource,
    Config.httpHeader(localStorage.getItem("mataccueilToken"),true)).pipe(
     tap((ressource: any) => console.log(`added ressource ${ressource}`))
   );
  }
  
  
  genPdf(ressource){
    return this.http.post<any>(`${Config.toApiUrl("genererpdf")}`, ressource,
     Config.httpHeader(localStorage.getItem("mataccueilToken"),true)).pipe(
      tap((ressource: any) => console.log(`added ressource ${ressource}`))
    );
  }

  
  transmettreReponse(ressource){
    return this.http.post<any>(`${Config.toApiUrl("requeteusager/transmettre/reponse")}`, ressource,
     Config.httpHeader(localStorage.getItem("mataccueilToken"),true)).pipe(
      tap((ressource: any) => console.log(`added ressource ${ressource}`))
    );
  }
   
  
  mailrelance(ressource){
    return this.http.post<any>(`${Config.toApiUrl("requeteusager/transmettre/relance")}`, ressource,
     Config.httpHeader(localStorage.getItem("mataccueilToken"),true)).pipe(
      tap((ressource: any) => console.log(`added ressource ${ressource}`))
    );
  }
   
  transfertRequet(ressource,id){
    return this.http.post<any>(`${Config.toApiUrl("requeteusager/transfert/entite")}/${id}`, ressource,
     Config.httpHeader(localStorage.getItem("mataccueilToken"),true)).pipe(
      tap((ressource: any) => console.log(`added ressource ${ressource}`))
    );
  }
  transfertRequetInterne(ressource,id){
    return this.http.post<any>(`${Config.toApiUrl("requeteusager/transfert/structure")}/${id}`, ressource,
     Config.httpHeader(localStorage.getItem("mataccueilToken"),true)).pipe(
      tap((ressource: any) => console.log(`added ressource ${ressource}`))
    );
  }
  relanceRequet(id){
    return this.http.get<any>(`${Config.toApiUrl("requeteusager/relance")}/${id}`,
     Config.httpHeader(localStorage.getItem("mataccueilToken"),true)).pipe(
      tap((ressource: any) => console.log(`added ressource ${ressource}`))
    );
  }
  relanceRequetType(id,idStru,idStrRelan){
    return this.http.get<any>(`${Config.toApiUrl("requeteusager/relanceType")}/${id}/${idStru}/${idStrRelan}`,
     Config.httpHeader(localStorage.getItem("mataccueilToken"),true)).pipe(
      tap((ressource: any) => console.log(`added ressource ${ressource}`))
    );
  }

  mailUsager(ressource){
    return this.http.post<any>(`${Config.toApiUrl("requeteusager/transmettre/reponse/rapide")}`, ressource,
     Config.httpHeader(localStorage.getItem("mataccueilToken"),true)).pipe(
      tap((ressource: any) => console.log(`added ressource ${ressource}`))
    );
  }
  getReponseRapide(id){
    return this.http.get<any>(`${Config.toApiUrl("requeteusager/mail/rapide/reponse")}/${id}/get`,
     Config.httpHeader(localStorage.getItem("mataccueilToken"),true)).pipe(
      tap((ressource: any) => console.log(`added ressource ${ressource}`))
    );
  }
  
  mailStructure(ressource){
    return this.http.post<any>(`${Config.toApiUrl("requeteusager/mail/rapide/structure")}`, ressource,
     Config.httpHeader(localStorage.getItem("mataccueilToken"),true)).pipe(
      tap((ressource: any) => console.log(`added ressource ${ressource}`))
    );
  }
  complementReponse(ressource){
    return this.http.post<any>(`${Config.toApiUrl("requeteusager/mail/rapide/reponse/complement")}`, ressource,
     Config.httpHeader(localStorage.getItem("mataccueilToken"),true)).pipe(
      tap((ressource: any) => console.log(`added ressource ${ressource}`))
    );
  }
  
  
  
  get(id){
    return this.http.get<any>(`${Config.toApiUrl("requeteusager/getprofil/")}${id}`, Config.httpHeader(localStorage.getItem("mataccueilToken"),true)).pipe(
      tap((ressource: any) => console.log(`get ressource ${ressource}`))
    );
  }
  create(ressource){
    return this.http.post<any>(`${Config.toApiUrl("requeteusager")}`, ressource,
     Config.httpHeader(localStorage.getItem("mataccueilToken"),true)).pipe(
      tap((ressource: any) => console.log(`added ressource ${ressource}`))
    );
  }
  update(ressource,id){
    return this.http.post<any>(`${Config.toApiUrl("requeteusager/")}${id}`, ressource,
     Config.httpHeader(localStorage.getItem("mataccueilToken"),true)).pipe(
      tap((ressource: any) => console.log(`upadted ressource ${ressource}`))
    );
  }
  delete(id:number){
    return this.http.delete<any[]>(`${Config.toApiUrl("requeteusager/")}${id}`,Config.httpHeader(localStorage.getItem("mataccueilToken"),false));
  }
}
