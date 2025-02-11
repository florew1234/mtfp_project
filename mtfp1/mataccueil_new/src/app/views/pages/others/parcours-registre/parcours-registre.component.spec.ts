import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { ParcoursRegistreComponent } from './parcours-registre.component';

describe('ParcoursRegistreComponent', () => {
  let component: ParcoursRegistreComponent;
  let fixture: ComponentFixture<ParcoursRegistreComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ ParcoursRegistreComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(ParcoursRegistreComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
