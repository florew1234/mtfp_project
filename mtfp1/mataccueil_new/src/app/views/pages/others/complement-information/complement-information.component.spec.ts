import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { ComplementInformationComponent } from './complement-information.component';

describe('ComplementInformationComponent', () => {
  let component: ComplementInformationComponent;
  let fixture: ComponentFixture<ComplementInformationComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ ComplementInformationComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(ComplementInformationComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
