import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { ListSynthesStructureComponent } from './list-synthes-structure.component';

describe('ListSynthesStructureComponent', () => {
  let component: ListSynthesStructureComponent;
  let fixture: ComponentFixture<ListSynthesStructureComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ ListSynthesStructureComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(ListSynthesStructureComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
