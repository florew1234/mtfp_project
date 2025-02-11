import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { ListetapesComponent } from './listetapes.component';

describe('ListetapesComponent', () => {
  let component: ListetapesComponent;
  let fixture: ComponentFixture<ListetapesComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ ListetapesComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(ListetapesComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
