import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { ListauxDigitComponent } from './list-taux-digita.component';

describe('ListauxDigitComponent', () => {
  let component: ListauxDigitComponent;
  let fixture: ComponentFixture<ListauxDigitComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ ListauxDigitComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(ListauxDigitComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
