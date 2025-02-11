import { TestBed } from '@angular/core/testing';

import { AdvancedStatisticsService } from './advanced-statistics.service';

describe('AdvancedStatisticsService', () => {
  let service: AdvancedStatisticsService;

  beforeEach(() => {
    TestBed.configureTestingModule({});
    service = TestBed.inject(AdvancedStatisticsService);
  });

  it('should be created', () => {
    expect(service).toBeTruthy();
  });
});
