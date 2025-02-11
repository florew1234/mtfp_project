import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { ListStatThemeComponent } from './list-stat-theme.component';

describe('ListStatThemeComponent', () => {
  let component: ListStatThemeComponent;
  let fixture: ComponentFixture<ListStatThemeComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ ListStatThemeComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(ListStatThemeComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
