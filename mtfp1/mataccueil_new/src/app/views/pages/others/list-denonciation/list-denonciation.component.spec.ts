import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { ListDenonciationComponent } from './list-denonciation.component';

describe('ListDenonciationComponent', () => {
  let component: ListDenonciationComponent;
  let fixture: ComponentFixture<ListDenonciationComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ ListDenonciationComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(ListDenonciationComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
