/* tslint:disable:no-unused-variable */
import { async, ComponentFixture, TestBed } from '@angular/core/testing';
import { By } from '@angular/platform-browser';
import { DebugElement } from '@angular/core';

import { PressComponent } from './press.component';

describe('PressComponent', () => {
  let component: PressComponent;
  let fixture: ComponentFixture<PressComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ PressComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(PressComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
