import { TestBed } from '@angular/core/testing';

import { RegisterguardGuard } from './registerguard.guard';

describe('RegisterguardGuard', () => {
  let guard: RegisterguardGuard;

  beforeEach(() => {
    TestBed.configureTestingModule({});
    guard = TestBed.inject(RegisterguardGuard);
  });

  it('should be created', () => {
    expect(guard).toBeTruthy();
  });
});
