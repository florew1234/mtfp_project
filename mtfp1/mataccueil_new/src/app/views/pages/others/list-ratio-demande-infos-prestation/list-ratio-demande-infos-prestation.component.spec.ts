import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { ListRatioDemandeInfosPrestationComponent } from './list-ratio-demande-infos-prestation.component';

describe('ListRatioDemandeInfosPrestationComponent', () => {
  let component: ListRatioDemandeInfosPrestationComponent;
  let fixture: ComponentFixture<ListRatioDemandeInfosPrestationComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ ListRatioDemandeInfosPrestationComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(ListRatioDemandeInfosPrestationComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
