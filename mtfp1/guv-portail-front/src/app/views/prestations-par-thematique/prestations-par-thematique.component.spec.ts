import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { PrestationsParThematiqueComponent } from './prestations-par-thematique.component';

describe('PrestationsParThematiqueComponent', () => {
  let component: PrestationsParThematiqueComponent;
  let fixture: ComponentFixture<PrestationsParThematiqueComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ PrestationsParThematiqueComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(PrestationsParThematiqueComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
