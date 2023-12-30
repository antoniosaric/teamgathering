/* tslint:disable:no-unused-variable */
import { async, ComponentFixture, TestBed } from '@angular/core/testing';
import { By } from '@angular/platform-browser';
import { DebugElement } from '@angular/core';

import { TagProjectComponent } from './tag-project.component';

describe('TagProjectComponent', () => {
  let component: TagProjectComponent;
  let fixture: ComponentFixture<TagProjectComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ TagProjectComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(TagProjectComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
