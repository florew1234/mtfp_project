import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { NatureContractComponent } from './nature-contract.component';

describe('NatureContractComponent', () => {
  let component: NatureContractComponent;
  let fixture: ComponentFixture<NatureContractComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ NatureContractComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(NatureContractComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
