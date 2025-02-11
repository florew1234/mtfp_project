import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { ListeserviceComponent } from './listeservice.component';

describe('ListeserviceComponent', () => {
  let component: ListeserviceComponent;
  let fixture: ComponentFixture<ListeserviceComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ ListeserviceComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(ListeserviceComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
