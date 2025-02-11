import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { ListeprofilsComponent } from './listeprofils.component';

describe('ListeprofilsComponent', () => {
  let component: ListeprofilsComponent;
  let fixture: ComponentFixture<ListeprofilsComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ ListeprofilsComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(ListeprofilsComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
