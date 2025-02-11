import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { PointPreoccupationComponent } from './point-preoccupation.component';

describe('PointPreoccupationComponent', () => {
  let component: PointPreoccupationComponent;
  let fixture: ComponentFixture<PointPreoccupationComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ PointPreoccupationComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(PointPreoccupationComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
