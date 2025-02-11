import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { ListRatioRequeteStructureComponent } from './list-ratio-requete-structure.component';

describe('ListRatioRequeteStructureComponent', () => {
  let component: ListRatioRequeteStructureComponent;
  let fixture: ComponentFixture<ListRatioRequeteStructureComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ ListRatioRequeteStructureComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(ListRatioRequeteStructureComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
