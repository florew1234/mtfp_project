import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { ListerdvparametreComponent } from './listerdvparametre.component';

describe('ListerdvparametreComponent', () => {
  let component: ListerdvparametreComponent;
  let fixture: ComponentFixture<ListerdvparametreComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ ListerdvparametreComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(ListerdvparametreComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
