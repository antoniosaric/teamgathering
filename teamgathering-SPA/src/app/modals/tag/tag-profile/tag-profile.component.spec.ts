/* tslint:disable:no-unused-variable */
import { async, ComponentFixture, TestBed } from '@angular/core/testing';
import { By } from '@angular/platform-browser';
import { DebugElement } from '@angular/core';

import { TagProfileComponent } from './tag-profile.component';

describe('TagProfileComponent', () => {
  let component: TagProfileComponent;
  let fixture: ComponentFixture<TagProfileComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ TagProfileComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(TagProfileComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
