import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { AlloRetraiteComponent } from './allo-retraite.component';

describe('AlloRetraiteComponent', () => {
  let component: AlloRetraiteComponent;
  let fixture: ComponentFixture<AlloRetraiteComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ AlloRetraiteComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(AlloRetraiteComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
