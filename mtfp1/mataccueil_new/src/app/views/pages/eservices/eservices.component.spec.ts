import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { EservicesComponent } from './eservices.component';

describe('EservicesComponent', () => {
  let component: EservicesComponent;
  let fixture: ComponentFixture<EservicesComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ EservicesComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(EservicesComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
