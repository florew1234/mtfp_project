import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { ListRequeteAdjointComponent } from './list-requete-adjoint.component';

describe('ListRequeteAdjointComponent', () => {
  let component: ListRequeteAdjointComponent;
  let fixture: ComponentFixture<ListRequeteAdjointComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ ListRequeteAdjointComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(ListRequeteAdjointComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
