import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { ListStatPrestationStructureComponent } from './list-stat-prestation-structure.component';

describe('ListStatPrestationStructureComponent', () => {
  let component: ListStatPrestationStructureComponent;
  let fixture: ComponentFixture<ListStatPrestationStructureComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ ListStatPrestationStructureComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(ListStatPrestationStructureComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
