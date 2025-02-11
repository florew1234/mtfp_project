import { TestBed } from '@angular/core/testing';

import { CcspServiceService } from './ccsp-service.service';

describe('CcspServiceService', () => {
  let service: CcspServiceService;

  beforeEach(() => {
    TestBed.configureTestingModule({});
    service = TestBed.inject(CcspServiceService);
  });

  it('should be created', () => {
    expect(service).toBeTruthy();
  });
});
