import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { ListRdvJourComponent } from './list-rdv-jour.component';

describe('ListRdvJourComponent', () => {
  let component: ListRdvJourComponent;
  let fixture: ComponentFixture<ListRdvJourComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ ListRdvJourComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(ListRdvJourComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
