import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { BaseConnaissanceComponent } from './base-connaissance.component';

describe('BaseConnaissanceComponent', () => {
  let component: BaseConnaissanceComponent;
  let fixture: ComponentFixture<BaseConnaissanceComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ BaseConnaissanceComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(BaseConnaissanceComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
