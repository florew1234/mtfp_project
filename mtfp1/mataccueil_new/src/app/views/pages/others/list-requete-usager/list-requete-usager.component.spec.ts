import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { ListRequeteUsagerComponent } from "./list-requete-usager.component";

describe('ListRequeteUsagerComponent', () => {
  let component: ListRequeteUsagerComponent;
  let fixture: ComponentFixture<ListRequeteUsagerComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ ListRequeteUsagerComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(ListRequeteUsagerComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
