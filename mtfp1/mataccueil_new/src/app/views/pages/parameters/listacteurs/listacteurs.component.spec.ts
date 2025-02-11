import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { ListacteursComponent } from './listacteurs.component';

describe('ListacteursComponent', () => {
  let component: ListacteursComponent;
  let fixture: ComponentFixture<ListacteursComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ ListacteursComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(ListacteursComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
