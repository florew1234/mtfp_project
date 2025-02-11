import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { ListRatioPlaintePrestaionComponent } from './list-ratio-plainte-prestaion.component';

describe('ListRatioPlaintePrestaionComponent', () => {
  let component: ListRatioPlaintePrestaionComponent;
  let fixture: ComponentFixture<ListRatioPlaintePrestaionComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ ListRatioPlaintePrestaionComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(ListRatioPlaintePrestaionComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
