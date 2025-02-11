import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { ListRatioDemandeInfosStructureComponent } from './list-ratio-demande-infos-structure.component';

describe('ListRatioDemandeInfosStructureComponent', () => {
  let component: ListRatioDemandeInfosStructureComponent;
  let fixture: ComponentFixture<ListRatioDemandeInfosStructureComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ ListRatioDemandeInfosStructureComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(ListRatioDemandeInfosStructureComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
