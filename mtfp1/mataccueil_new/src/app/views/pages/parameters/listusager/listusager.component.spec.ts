import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { ListusagerComponent } from './listusager.component';

describe('ListusagerComponent', () => {
  let component: ListusagerComponent;
  let fixture: ComponentFixture<ListusagerComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ ListusagerComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(ListusagerComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
