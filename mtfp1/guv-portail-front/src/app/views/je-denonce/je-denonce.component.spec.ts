import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { JeDenonceComponent } from './je-denonce.component';

describe('JeDenonceComponent', () => {
  let component: JeDenonceComponent;
  let fixture: ComponentFixture<JeDenonceComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ JeDenonceComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(JeDenonceComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
