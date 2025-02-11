import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { ListdaterdvComponent } from './listdaterdv.component';

describe('ListdaterdvComponent', () => {
  let component: ListdaterdvComponent;
  let fixture: ComponentFixture<ListdaterdvComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ ListdaterdvComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(ListdaterdvComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
