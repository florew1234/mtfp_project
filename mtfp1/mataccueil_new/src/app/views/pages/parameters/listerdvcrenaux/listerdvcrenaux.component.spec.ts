import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { ListerdvcrenauxComponent } from './listerdvcrenaux.component';

describe('ListerdvcrenauxComponent', () => {
  let component: ListerdvcrenauxComponent;
  let fixture: ComponentFixture<ListerdvcrenauxComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ ListerdvcrenauxComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(ListerdvcrenauxComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
