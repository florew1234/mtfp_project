export class Profil {
    CodeProfil:          string;
    id:          number;
    idEntite:number
    LibelleProfil:     string;
    code:        string;
    saisie:      boolean;
    saisiePoint: boolean;
    niveauValidation:  boolean;
    sgm:         boolean;
    dc:          boolean;
    inspection:boolean
    ministre:    boolean;
    direction:   boolean;
    decisionnel_suivi:boolean;
    pointfocal:boolean;
    pointfocalcom:boolean;
    service:     boolean;
    saisie_adjoint:boolean
    superadmin:boolean
    admin_sectoriel:boolean
    division:    boolean;
    usersimple:  boolean;
    ratio:       boolean;

    constructor(attrs: any = null) {
        if (attrs) {
          this.build(attrs);
        }
      }

      build(attrs: any): void {
       
        this.code = attrs.code;
        this.id=attrs.id;
        this.LibelleProfil=attrs.LibelleProfil
        this.saisie= attrs.saisie==1 ? true : false;
        this.saisiePoint= attrs.saisiePoint==1 ? true : false;
        this.niveauValidation= attrs.niveauValidation==1 ? true : false;
        this.sgm= attrs.sgm==1 ? true : false;
        this.dc= attrs.dc==1 ? true : false;
        this.ministre= attrs.ministre==1 ? true : false;
        this.direction= attrs.direction==1 ? true : false;
        this.service = attrs.service==1 ? true : false;
        this.division= attrs.division==1 ? true : false;
        this.usersimple= attrs.usersimple==1 ? true : false;
        this.ratio= attrs.ratio==1 ? true : false;
        this.inspection= attrs.inspection==1 ? true : false;
        
        this.saisie_adjoint= attrs.saisie_adjoint==1 ? true : false;
        this.pointfocal= attrs.pointfocal==1 ? true : false;
        this.pointfocalcom= attrs.pointfocalcom==1 ? true : false;
        this.decisionnel_suivi= attrs.decisionnel_suivi==1 ? true : false;
      }
    toJson() {
        return {
            id:this.id,
            LibelleProfil: this.LibelleProfil,  
            code: this.code,  
            saisie:      this.saisie,
            saisiePoint: this.saisiePoint,
            niveauValidation:  this.niveauValidation,
            sgm:         this.sgm,
            decisionnel_suivi:this.decisionnel_suivi,
            pointfocal:this.pointfocal,
            pointfocalcom:this.pointfocalcom,
            dc:          this.dc,
            ministre:    this.ministre,
            direction:   this.direction,
            service:     this.service,
            inspection:this.inspection,
            division:    this.division,
            usersimple:  this.usersimple,
            saisie_adjoint:this.saisie_adjoint,
            ratio:       this.ratio
          };
    }
}
