import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { RelanceComponent } from './relance.component';

describe('RelanceComponent', () => {
  let component: RelanceComponent;
  let fixture: ComponentFixture<RelanceComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ RelanceComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(RelanceComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
