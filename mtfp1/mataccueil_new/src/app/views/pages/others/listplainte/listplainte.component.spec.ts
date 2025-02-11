import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { ListplainteComponent } from './listplainte.component';

describe('ListplainteComponent', () => {
  let component: ListplainteComponent;
  let fixture: ComponentFixture<ListplainteComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ ListplainteComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(ListplainteComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
