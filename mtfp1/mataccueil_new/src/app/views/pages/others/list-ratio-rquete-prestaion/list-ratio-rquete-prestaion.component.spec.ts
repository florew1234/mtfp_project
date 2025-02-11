import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { ListRatioRquetePrestaionComponent } from './list-ratio-rquete-prestaion.component';

describe('ListRatioRquetePrestaionComponent', () => {
  let component: ListRatioRquetePrestaionComponent;
  let fixture: ComponentFixture<ListRatioRquetePrestaionComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ ListRatioRquetePrestaionComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(ListRatioRquetePrestaionComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
