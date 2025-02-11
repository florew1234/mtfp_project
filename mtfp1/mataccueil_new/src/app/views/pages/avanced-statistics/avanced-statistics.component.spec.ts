import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { AvancedStatisticsComponent } from './avanced-statistics.component';

describe('AvancedStatisticsComponent', () => {
  let component: AvancedStatisticsComponent;
  let fixture: ComponentFixture<AvancedStatisticsComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ AvancedStatisticsComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(AvancedStatisticsComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
