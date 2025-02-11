import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { ReclammationComponent } from './reclammation.component';

describe('ReclammationComponent', () => {
  let component: ReclammationComponent;
  let fixture: ComponentFixture<ReclammationComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ ReclammationComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(ReclammationComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
