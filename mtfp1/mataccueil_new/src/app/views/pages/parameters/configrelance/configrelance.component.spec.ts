import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { ConfigrelanceComponent } from './configrelance.component';

describe('ConfigrelanceComponent', () => {
  let component: ConfigrelanceComponent;
  let fixture: ComponentFixture<ConfigrelanceComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ ConfigrelanceComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(ConfigrelanceComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
