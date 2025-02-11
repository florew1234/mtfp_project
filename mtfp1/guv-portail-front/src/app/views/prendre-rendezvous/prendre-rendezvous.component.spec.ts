import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { PrendreRendezvousComponent } from './prendre-rendezvous.component';

describe('PrendreRendezvousComponent', () => {
  let component: PrendreRendezvousComponent;
  let fixture: ComponentFixture<PrendreRendezvousComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ PrendreRendezvousComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(PrendreRendezvousComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
