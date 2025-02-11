import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { ListRatioPlainteStructureComponent } from './list-ratio-plainte-structure.component';

describe('ListRatioPlainteStructureComponent', () => {
  let component: ListRatioPlainteStructureComponent;
  let fixture: ComponentFixture<ListRatioPlainteStructureComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ ListRatioPlainteStructureComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(ListRatioPlainteStructureComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
