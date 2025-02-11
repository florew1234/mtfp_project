import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { GraphiquetypeComponent } from './graphiquetype.component';

describe('GraphiquetypeComponent', () => {
  let component: GraphiquetypeComponent;
  let fixture: ComponentFixture<GraphiquetypeComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ GraphiquetypeComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(GraphiquetypeComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
