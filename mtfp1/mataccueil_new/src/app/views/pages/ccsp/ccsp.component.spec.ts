import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { CcspComponent } from './ccsp.component';

describe('CcspComponent', () => {
  let component: CcspComponent;
  let fixture: ComponentFixture<CcspComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ CcspComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(CcspComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
