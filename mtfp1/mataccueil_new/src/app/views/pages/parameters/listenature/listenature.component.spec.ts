import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { ListenatureComponent } from './listenature.component';

describe('ListenatureComponent', () => {
  let component: ListenatureComponent;
  let fixture: ComponentFixture<ListenatureComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ ListenatureComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(ListenatureComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
