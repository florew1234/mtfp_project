import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { ParcoursRequeteComponent } from './parcours-requete.component';

describe('ParcoursRequeteComponent', () => {
  let component: ParcoursRequeteComponent;
  let fixture: ComponentFixture<ParcoursRequeteComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ ParcoursRequeteComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(ParcoursRequeteComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
