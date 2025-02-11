import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { BaseUsagerComponent } from './base-usager.component';

describe('BaseUsagerComponent', () => {
  let component: BaseUsagerComponent;
  let fixture: ComponentFixture<BaseUsagerComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ BaseUsagerComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(BaseUsagerComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
