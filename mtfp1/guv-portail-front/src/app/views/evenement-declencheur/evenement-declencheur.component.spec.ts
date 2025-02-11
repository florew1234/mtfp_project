import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { EvenementDeclencheurComponent } from './evenement-declencheur.component';

describe('EvenementDeclencheurComponent', () => {
  let component: EvenementDeclencheurComponent;
  let fixture: ComponentFixture<EvenementDeclencheurComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ EvenementDeclencheurComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(EvenementDeclencheurComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
