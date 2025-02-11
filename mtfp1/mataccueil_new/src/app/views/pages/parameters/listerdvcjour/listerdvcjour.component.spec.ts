import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { ListerdvcjourComponent } from './listerdvcjour.component';

describe('ListerdvcjourComponent', () => {
  let component: ListerdvcjourComponent;
  let fixture: ComponentFixture<ListerdvcjourComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ ListerdvcjourComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(ListerdvcjourComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
