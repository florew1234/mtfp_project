import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { GraphiquestructureComponent } from './graphiquestructure.component';

describe('GraphiquestructureComponent', () => {
  let component: GraphiquestructureComponent;
  let fixture: ComponentFixture<GraphiquestructureComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ GraphiquestructureComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(GraphiquestructureComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
