import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { ListefonctionComponent } from './listefonction.component';

describe('ListefonctionComponent', () => {
  let component: ListefonctionComponent;
  let fixture: ComponentFixture<ListefonctionComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ ListefonctionComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(ListefonctionComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
