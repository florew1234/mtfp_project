import { TestBed } from '@angular/core/testing';

import { NatureContractService } from './nature-contract.service';

describe('NatureContractService', () => {
  let service: NatureContractService;

  beforeEach(() => {
    TestBed.configureTestingModule({});
    service = TestBed.inject(NatureContractService);
  });

  it('should be created', () => {
    expect(service).toBeTruthy();
  });
});
