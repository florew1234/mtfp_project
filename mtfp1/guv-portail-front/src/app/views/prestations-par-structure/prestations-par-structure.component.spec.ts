import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { PrestationsParStructureComponent } from './prestations-par-structure.component';

describe('PrestationsParStructureComponent', () => {
  let component: PrestationsParStructureComponent;
  let fixture: ComponentFixture<PrestationsParStructureComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ PrestationsParStructureComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(PrestationsParStructureComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
