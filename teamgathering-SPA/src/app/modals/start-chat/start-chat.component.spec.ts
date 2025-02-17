/* tslint:disable:no-unused-variable */
import { async, ComponentFixture, TestBed } from '@angular/core/testing';
import { By } from '@angular/platform-browser';
import { DebugElement } from '@angular/core';

import { StartChatComponent } from './start-chat.component';

describe('StartChatComponent', () => {
  let component: StartChatComponent;
  let fixture: ComponentFixture<StartChatComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ StartChatComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(StartChatComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
