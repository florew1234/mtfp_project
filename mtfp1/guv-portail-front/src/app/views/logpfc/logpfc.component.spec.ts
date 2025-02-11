import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { LogpfcComponent } from './logpfc.component';

describe('LogpfcComponent', () => {
  let component: LogpfcComponent;
  let fixture: ComponentFixture<LogpfcComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ LogpfcComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(LogpfcComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
