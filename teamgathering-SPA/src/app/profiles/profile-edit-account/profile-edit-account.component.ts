import { Component, OnInit, ViewChild } from '@angular/core';
import { FormBuilder, Validators, FormGroup } from '@angular/forms';
import { ActivatedRoute, Router } from '@angular/router';
import { AuthService } from 'src/app/_services/auth.service';
import { AccountService } from 'src/app/_services/account.service';
import { AlertifyService } from 'src/app/_services/alertify.service';

@Component({
  selector: 'app-profile-edit-account',
  templateUrl: './profile-edit-account.component.html',
  styleUrls: ['./profile-edit-account.component.css']
})
export class ProfileEditAccountComponent implements OnInit {
  @ViewChild('emailForm', {static:true}) emailForm: FormGroup;
  @ViewChild('passwordForm', {static:true}) passwordForm: FormGroup;
  @ViewChild('deleteAccountForm', {static:true}) deleteAccountForm: FormGroup;
  emailState = false;
  passwordState = false;
  deleteState = false;

  email: '';

  constructor(
    private fb: FormBuilder, 
    private route: ActivatedRoute, 
    private authService: AuthService,
    private alertify: AlertifyService,
    private router: Router,
    private accountService: AccountService) { }

  ngOnInit() {
    this.email = this.authService.decodedToken.data.email;
    this.createEditForm();
  }

  createEditForm(){
    this.emailForm = this.fb.group({
      email: ['', [ Validators.required, Validators.email ] ],
      password: ['', [Validators.required, Validators.minLength(4), Validators.maxLength(16)]]
    })

    this.passwordForm = this.fb.group({
      password: ['', [Validators.required, Validators.minLength(4), Validators.maxLength(16)]],
      newPassword: ['', [Validators.required, Validators.minLength(4), Validators.maxLength(16)]],
      confirmPassword: ['', Validators.required]
    }, {validator: this.passwordMatchValidator})

    this.deleteAccountForm = this.fb.group({
      deleteText: ['', [ Validators.required, Validators.minLength(5), Validators.maxLength(6), Validators.pattern('(?:^|\W)DELETE(?:$|\W)') ] ],
      password: ['', [Validators.required, Validators.minLength(4), Validators.maxLength(16)]]
    })
  }

  passwordMatchValidator( g: FormGroup ){
      return g.get('newPassword').value === g.get('confirmPassword').value ? null : { 'mismatch': true }
  }
  
  updateEmail(){
    if( this.emailForm.valid ){
      this.accountService.updateEmail( { 'token': localStorage.getItem('token') }, this.emailForm.value).subscribe(next => {
        this.authService.setToken(next);
        this.email = this.emailForm.value.email;
        this.alertify.success('update email successful');
        this.emailForm.reset();
      }, error => {
        this.alertify.error(error);
      }, () =>{
      });
    }
  }

  updatePassword(){
    if( this.passwordForm.valid ){
      this.accountService.updatePassword( { 'token': localStorage.getItem('token') }, this.passwordForm.value).subscribe(next => {
        this.authService.setToken(next);
        this.alertify.success('update password successful');
        this.passwordForm.reset();
      }, error => {
        this.alertify.error(error);
      }, () =>{
      });
    }
  }

  deleteAccount(){
    if( this.deleteAccountForm.valid ){
      this.accountService.updatePassword( { 'token': localStorage.getItem('token') }, this.deleteAccountForm.value).subscribe(() => {
        this.alertify.success('account deleted');
        this.deleteAccountForm.reset();
      }, error => {
        this.alertify.error(error);
      }, () =>{
        this.authService.logout();
      });
    }
  }

  changePasswordState(event){
    this.passwordForm.reset();
    this.passwordState = event;
  }

  changeEmailState(event){
    this.emailForm.reset();
    this.emailState = event;
  }

  changeDeleteState(event){
    this.deleteAccountForm.reset();
    this.deleteState = event;
  }

}
