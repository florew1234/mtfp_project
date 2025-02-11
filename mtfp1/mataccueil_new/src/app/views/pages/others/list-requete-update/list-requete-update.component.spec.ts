import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { ListRequeteUpdateComponent } from './list-requete-update.component';

describe('ListRequeteUpdateComponent', () => {
  let component: ListRequeteUpdateComponent;
  let fixture: ComponentFixture<ListRequeteUpdateComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ ListRequeteUpdateComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(ListRequeteUpdateComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
