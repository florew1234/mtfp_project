import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { EspaceusagerComponent } from './espaceusager.component';

describe('EspaceusagerComponent', () => {
  let component: EspaceusagerComponent;
  let fixture: ComponentFixture<EspaceusagerComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ EspaceusagerComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(EspaceusagerComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
