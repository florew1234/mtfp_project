import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { PointReponseComponent } from './point-reponse.component';

describe('PointReponseComponent', () => {
  let component: PointReponseComponent;
  let fixture: ComponentFixture<PointReponseComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ PointReponseComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(PointReponseComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
