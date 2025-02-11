import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { ListserviceatraiterComponent } from './listserviceatraiter.component';

describe('ListserviceatraiterComponent', () => {
  let component: ListserviceatraiterComponent;
  let fixture: ComponentFixture<ListserviceatraiterComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ ListserviceatraiterComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(ListserviceatraiterComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
