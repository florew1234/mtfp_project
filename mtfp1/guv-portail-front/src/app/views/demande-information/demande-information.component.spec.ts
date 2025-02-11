import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { DemandeInformationComponent } from './demande-information.component';

describe('DemandeInformationComponent', () => {
  let component: DemandeInformationComponent;
  let fixture: ComponentFixture<DemandeInformationComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ DemandeInformationComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(DemandeInformationComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
