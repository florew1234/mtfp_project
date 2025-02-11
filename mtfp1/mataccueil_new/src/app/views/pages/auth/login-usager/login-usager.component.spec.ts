import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { LoginUsagerComponent } from './login-usager.component';

describe('LoginUsagerComponent', () => {
  let component: LoginUsagerComponent;
  let fixture: ComponentFixture<LoginUsagerComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ LoginUsagerComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(LoginUsagerComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
