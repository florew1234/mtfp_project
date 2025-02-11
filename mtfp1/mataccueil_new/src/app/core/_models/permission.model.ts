import { ModelInterface } from "./model.interface";

export class Permission implements ModelInterface{
    id:number;
    name: string;
    code: string;
    created_at: string;
 

    constructor(attrs: any = null) {
        if (attrs) {
          this.build(attrs);
        }
      }

      build(attrs: any): void {
        this.name = attrs.name;
        this.code = attrs.code;
        this.id=attrs.id;
        this.created_at = attrs.created_at;
       
      }
    toJson() {
        return {
            id:this.id,
            name: this.name,  
            code: this.code,  
            created_at: this.created_at,
          };
    }
}
