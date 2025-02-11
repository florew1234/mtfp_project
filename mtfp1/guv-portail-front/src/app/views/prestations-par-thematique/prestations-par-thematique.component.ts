import { Component, OnInit } from '@angular/core';
import { PdaService } from '../../core/_services/pda.servic';
import { ActivatedRoute } from '@angular/router';

@Component({
  selector: 'app-prestations-par-thematique',
  templateUrl: './prestations-par-thematique.component.html',
  styleUrls: ['./prestations-par-thematique.component.css']
})
export class PrestationsParThematiqueComponent implements OnInit {

  searchText=""
  data=[]
  thematiques=[]
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
    this.thematiques=[]
    this.pdaService.getThematiques().subscribe(
      (res:any)=>{
        this.thematiques=res
      },)
    this.loading=true
    this.data=[]
    this._temp=[]
    alert(this.route.snapshot.paramMap.get('param'))
    if(this.route.snapshot.paramMap.get('param')!=null){
      this.pdaService.getPrestationsByThematique(this.route.snapshot.paramMap.get('param')).subscribe(
        (res:any)=>{
                this.data=res
                this.loading=false
        },)
    }else{
      this.pdaService.getPrestations().subscribe(
        (res:any)=>{
                this.data=res
                this._temp=this.data
                this.collectionSize=this.data.length
                this.loading=false
        },)
    }
    
  }

  filter(event){
    this.data=[]
    if(event.target.value=="0"){
      this.data=this._temp
    }else{
      this.data=this._temp.filter(e=> (e.idType==event.target.value))
    }
    this.searchText=""
    this.collectionSize=this.data.length
  }

  onActivate() {
    window.scroll(0,0);
  }
}
