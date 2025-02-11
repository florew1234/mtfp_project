import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { ListRequeteDivisionComponent } from './list-requete-division.component';

describe('ListRequeteDivisionComponent', () => {
  let component: ListRequeteDivisionComponent;
  let fixture: ComponentFixture<ListRequeteDivisionComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ ListRequeteDivisionComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(ListRequeteDivisionComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
