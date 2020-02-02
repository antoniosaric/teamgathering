import { Component, OnInit, EventEmitter, Output } from '@angular/core';
import { FormGroup, FormControl, Validators, FormBuilder } from '@angular/forms';


@Component({
  selector: 'app-forgot-password',
  templateUrl: './forgot-password.component.html',
  styleUrls: ['./forgot-password.component.css']
})
export class ForgotPasswordComponent implements OnInit {
  forgotPassword: FormGroup;
  forgotPasswordSetPassword: FormGroup;
  forgotPasswordModalState = '1';
  @Output() registerToggleSize = new EventEmitter<string>();

  constructor(
    private fb: FormBuilder
  ) { }

  ngOnInit() {
    this.createForgotPasswordForm();
    this.createForgotPasswordSetPasswordForm();
  }

  createForgotPasswordForm(){
    this.forgotPassword = this.fb.group({
      email: ['', [ Validators.required, Validators.email ] ]
    })
  }

  createForgotPasswordSetPasswordForm(){
    this.forgotPasswordSetPassword= this.fb.group({
      key: ['', [ Validators.required ] ],
      password: ['', [Validators.required, Validators.minLength(4), Validators.maxLength(16)]],
      confirmPassword: ['', Validators.required],
    }, {validator: this.passwordMatchValidator})
  }

  passwordMatchValidator( g: FormGroup ){
      return g.get('password').value === g.get('confirmPassword').value ? null : { 'mismatch': true }
  }

  forgotPasswordSend(){
    console.log('sent');
  }

}
