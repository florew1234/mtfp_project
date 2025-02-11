import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { GraphiqueevolutionComponent } from './graphiqueevolution.component';

describe('GraphiqueevolutionComponent', () => {
  let component: GraphiqueevolutionComponent;
  let fixture: ComponentFixture<GraphiqueevolutionComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ GraphiqueevolutionComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(GraphiqueevolutionComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
