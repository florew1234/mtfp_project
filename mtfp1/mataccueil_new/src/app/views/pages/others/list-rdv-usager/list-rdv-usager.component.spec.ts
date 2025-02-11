import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { ListRdvUsagerComponent } from './list-rdv-usager.component';

describe('ListRdvUsagerComponent', () => {
  let component: ListRdvUsagerComponent;
  let fixture: ComponentFixture<ListRdvUsagerComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ ListRdvUsagerComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(ListRdvUsagerComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
