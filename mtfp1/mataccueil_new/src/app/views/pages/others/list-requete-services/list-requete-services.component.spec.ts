import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { ListRequeteServicesComponent } from './list-requete-services.component';

describe('ListRequeteServicesComponent', () => {
  let component: ListRequeteServicesComponent;
  let fixture: ComponentFixture<ListRequeteServicesComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ ListRequeteServicesComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(ListRequeteServicesComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
