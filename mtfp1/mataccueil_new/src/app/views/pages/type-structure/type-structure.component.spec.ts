import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { TypeStructureComponent } from './type-structure.component';

describe('TypeStructureComponent', () => {
  let component: TypeStructureComponent;
  let fixture: ComponentFixture<TypeStructureComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ TypeStructureComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(TypeStructureComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
