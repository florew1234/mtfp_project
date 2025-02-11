import { Component, OnInit } from '@angular/core';
import { PdaService } from '../../core/_services/pda.servic';
import { ActivatedRoute } from '@angular/router';

@Component({
  selector: 'app-prestations-par-structure',
  templateUrl: './prestations-par-structure.component.html',
  styleUrls: ['./prestations-par-structure.component.css']
})
export class PrestationsParStructureComponent implements OnInit {

  searchText=""
  data=[]
  structures=[]
  _temp=[]
  selected_key=""
  collectionSize=0
  page=1
  pageSize=15

  loading=false
  search(){
    this.data=this._temp.filter(r => {
      const term = this.searchText.toLowerCase();
      return r.libelle.toLowerCase().includes(term) 
    })
    this.selected_key=""
    this.collectionSize=this.data.length
  }
  constructor(private pdaService:PdaService,private route:ActivatedRoute) { }

  ngOnInit(): void {
    window.scroll(0,0);

    this.structures=[]
    this.pdaService.getStructures_new(1).subscribe(
      (res:any)=>{
        // this.structures=res.filter(e=> (e.services.length!=0))
        this.structures=res
        // console.log(res)
      },)

    this.loading=true
    this.data=[]
    this._temp=[]
    if(this.route.snapshot.paramMap.get('param')!=null){
      
      this.pdaService.getPrestationsByStructure(this.route.snapshot.paramMap.get('param')).subscribe(
        (res:any)=>{
                this.data=res
                this.loading=false
        },)
    }else{
      this.pdaService.getPrestations().subscribe(
        (res:any)=>{
            this.data=res.filter(e=>{
              if(e.service_parent != null){
                return (e.service_parent.type_s=='dt' && e.service_parent.active=='1')
              }
            })
            this._temp=this.data
            this.collectionSize=this.data.length
            this.loading=false
            // console.log(res)

        },)
      // this.pdaService.getPrestations().subscribe(
      //   (res:any)=>{
      //           this.data=res.filter(e=> (e.service_parent != null ? e.service_parent.type_s=='dt' && e.service_parent.active=='1' : ''))
      //           this._temp=this.data
      //           this.collectionSize=this.data.length
      //           this.loading=false
      //           console.log(res)
      //   },)
    }
    
  }

  filter(event){
    this.data=[]
    if(event.target.value=="0"){
      this.data=this._temp
    }else{
      this.data=this._temp.filter(e=> (e.idParent==event.target.value))
    }
    this.searchText=""
    this.collectionSize=this.data.length
  }

  onActivate() {
    window.scroll(0,0);
  }

}
