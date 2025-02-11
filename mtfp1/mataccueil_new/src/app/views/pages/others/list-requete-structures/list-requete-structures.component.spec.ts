import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { ListRequeteStructuresComponent } from './list-requete-structures.component';

describe('ListRequeteStructuresComponent', () => {
  let component: ListRequeteStructuresComponent;
  let fixture: ComponentFixture<ListRequeteStructuresComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ ListRequeteStructuresComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(ListRequeteStructuresComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
