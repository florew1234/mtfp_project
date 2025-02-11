import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { ListtypeComponent } from './listtype.component';

describe('ListtypeComponent', () => {
  let component: ListtypeComponent;
  let fixture: ComponentFixture<ListtypeComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ ListtypeComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(ListtypeComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
