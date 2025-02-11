import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { AttributcomComponent } from './attributcom.component';

describe('AttributcomComponent', () => {
  let component: AttributcomComponent;
  let fixture: ComponentFixture<AttributcomComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ AttributcomComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(AttributcomComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
