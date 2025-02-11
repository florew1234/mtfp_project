import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { StatspreocComponent } from './stats-preoc.component';

describe('StatspreocComponent', () => {
  let component: StatspreocComponent;
  let fixture: ComponentFixture<StatspreocComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ StatspreocComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(StatspreocComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
