import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { InfoCarriereComponent } from './info-carriere.component';

describe('InfoCarriereComponent', () => {
  let component: InfoCarriereComponent;
  let fixture: ComponentFixture<InfoCarriereComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ InfoCarriereComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(InfoCarriereComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
