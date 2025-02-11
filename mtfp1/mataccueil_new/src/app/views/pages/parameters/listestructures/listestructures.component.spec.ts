import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { ListestructuresComponent } from './listestructures.component';

describe('ListestructuresComponent', () => {
  let component: ListestructuresComponent;
  let fixture: ComponentFixture<ListestructuresComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ ListestructuresComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(ListestructuresComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
