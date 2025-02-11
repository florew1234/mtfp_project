import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { MailCheckResendComponent } from './mail-check-resend.component';

describe('MailCheckResendComponent', () => {
  let component: MailCheckResendComponent;
  let fixture: ComponentFixture<MailCheckResendComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ MailCheckResendComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(MailCheckResendComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
