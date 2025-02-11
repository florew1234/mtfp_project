import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { RequetesComponent } from './requetes.component';

describe('RequetesComponent', () => {
  let component: RequetesComponent;
  let fixture: ComponentFixture<RequetesComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ RequetesComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(RequetesComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
