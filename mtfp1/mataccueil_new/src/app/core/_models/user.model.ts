import { ModelInterface } from "./model.interface";
import { Roles } from './roles';
import { Permission } from './permission.model';

export class User implements ModelInterface {
    id: number;
    country: string;
    idagent: number;
    idprofil: string;
    name: string;
    agent_user: any;
    profil_user: any;
    avatar: string;
    email: string;
    statut: number;
    token: string;
    sex: string;
    job: string;
    created_at: string;
    role ?: string;
    permissions:[]
    

    constructor(attrs: any = null) {
        if (attrs) {
          this.build(attrs);
        }
      }

      build(attrs: any): void {
        this.id = attrs.id;
        this.statut = attrs.statut;
        this.email = attrs.email;
        this.idagent = attrs.idagent;
        this.idprofil = attrs.idprofil;
        this.name = attrs.name;
        this.agent_user = attrs.agent_user;
        this.profil_user = attrs.profil_user;
        this.avatar = attrs.avatar;
        this.sex = attrs.sex;
        this.country = attrs.country;
        this.job = attrs.job;
        this.token = attrs.token;
        this.created_at = attrs.created_at;
        this.role = attrs.role;
        this.permissions = attrs.permissions;
      }
    toJson() {
        return {
            id: this.id,
            statut: this.statut,
            email: this.email,
            idagent: this.idagent,
            idprofil: this.idprofil,
            agent_user: this.agent_user,
            profil_user: this.profil_user,
            avatar: this.avatar,
            sex: this.sex,
            country: this.country,
            job: this.job,
            name:this.name,
            token: this.token,
            created_at: this.created_at,
            role: this.role,
            permissions:this.permissions

          };
    }

    

}
