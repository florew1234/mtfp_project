import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { ManageRequeteUsagerComponent } from './manage-requete-usager.component';

describe('ManageRequeteUsagerComponent', () => {
  let component: ManageRequeteUsagerComponent;
  let fixture: ComponentFixture<ManageRequeteUsagerComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ ManageRequeteUsagerComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(ManageRequeteUsagerComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
