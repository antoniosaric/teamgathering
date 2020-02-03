import { Component, OnInit, EventEmitter, Output, Input } from '@angular/core';
import { FormGroup, FormControl, Validators, FormBuilder } from '@angular/forms';
import { AuthService } from '../../_services/auth.service';
import { AlertifyService } from '../../_services/alertify.service';


@Component({
  selector: 'app-forgot-password',
  templateUrl: './forgot-password.component.html',
  styleUrls: ['./forgot-password.component.css']
})
export class ForgotPasswordComponent implements OnInit {
  forgotPassword: FormGroup;
  forgotPasswordSetPassword: FormGroup;
  modalState = '1'; 
  @Output() onSetState = new EventEmitter<boolean>();

  constructor(
    private authService: AuthService, 
    private alertify: AlertifyService, 
    private fb: FormBuilder,
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
    this.forgotPasswordSetPassword = this.fb.group({
      key: ['', [ Validators.required ] ],
      password: ['', [Validators.required, Validators.minLength(4), Validators.maxLength(16)]],
      confirmPassword: ['', Validators.required],
    }, {validator: this.passwordMatchValidator})
  }

  passwordMatchValidator( g: FormGroup ){
      return g.get('password').value === g.get('confirmPassword').value ? null : { 'mismatch': true }
  }

  forgotPasswordSend(){
    if( this.forgotPassword.valid ){
      console.log(this.forgotPassword.value )
      this.authService.sendForgotPasswordEmail( this.forgotPassword.value ).subscribe(next => {
        this.alertify.success('email sent');
        this.forgotPassword.reset();
        this.modalState = '2';
      }, error => {
        this.alertify.error(error);
      }, () =>{
      });
    } 
  }

  forgotPasswordSetPasswordSend(){
    this.onSetState.emit(false);
  }

  closeModal(){
    this.onSetState.emit(false);
  }

  goBack(){
    this.modalState = '1';
  }

}
