import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { ListeinstitutionComponent } from './listeinstitution.component';

describe('ListeinstitutionComponent', () => {
  let component: ListeinstitutionComponent;
  let fixture: ComponentFixture<ListeinstitutionComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ ListeinstitutionComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(ListeinstitutionComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
