import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { ListStatStructureComponent } from './list-stat-structure.component';

describe('ListStatStructureComponent', () => {
  let component: ListStatStructureComponent;
  let fixture: ComponentFixture<ListStatStructureComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ ListStatStructureComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(ListStatStructureComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
