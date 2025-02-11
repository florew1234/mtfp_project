import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { MailCheckComponent } from './mail-check.component';

describe('MailCheckComponent', () => {
  let component: MailCheckComponent;
  let fixture: ComponentFixture<MailCheckComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ MailCheckComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(MailCheckComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
