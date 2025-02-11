import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { ListStatReponseComponent } from './list-stat-reponse.component';

describe('ListStatReponseComponent', () => {
  let component: ListStatReponseComponent;
  let fixture: ComponentFixture<ListStatReponseComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ ListStatReponseComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(ListStatReponseComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
