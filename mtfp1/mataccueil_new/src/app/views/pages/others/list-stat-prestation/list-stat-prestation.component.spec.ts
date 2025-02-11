import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { ListStatPrestationComponent } from './list-stat-prestation.component';

describe('ListStatPrestationComponent', () => {
  let component: ListStatPrestationComponent;
  let fixture: ComponentFixture<ListStatPrestationComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ ListStatPrestationComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(ListStatPrestationComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
