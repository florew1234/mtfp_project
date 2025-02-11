import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { NotifPanelComponent } from './notif-panel.component';

describe('NotifPanelComponent', () => {
  let component: NotifPanelComponent;
  let fixture: ComponentFixture<NotifPanelComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ NotifPanelComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(NotifPanelComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
