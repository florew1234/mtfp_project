import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { InfoPensionComponent } from './info-pension.component';

describe('InfoPensionComponent', () => {
  let component: InfoPensionComponent;
  let fixture: ComponentFixture<InfoPensionComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ InfoPensionComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(InfoPensionComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
