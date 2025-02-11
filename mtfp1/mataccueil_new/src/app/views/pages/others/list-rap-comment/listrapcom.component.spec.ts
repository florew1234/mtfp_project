import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { RapCommentComponent } from './listrapcom.component';

describe('RapCommentComponent', () => {
  let component: RapCommentComponent;
  let fixture: ComponentFixture<RapCommentComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ RapCommentComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(RapCommentComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
