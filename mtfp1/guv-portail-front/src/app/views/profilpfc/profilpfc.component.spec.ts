import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { ProfilpfcComponent } from './profilpfc.component';

describe('ProfilpfcComponent', () => {
  let component: ProfilpfcComponent;
  let fixture: ComponentFixture<ProfilpfcComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ ProfilpfcComponent ]
    })
    .compileComponents();
  }));

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
