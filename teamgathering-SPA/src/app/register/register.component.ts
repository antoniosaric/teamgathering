import { Component, OnInit, EventEmitter, Output } from '@angular/core';
import { AuthService } from '../_services/auth.service';
import { AlertifyService } from '../_services/alertify.service';
import { FormGroup, FormControl, Validators, FormBuilder } from '@angular/forms';
import { Profile } from '../_models/profile';
import { Router } from '@angular/router';

@Component({
  selector: 'app-register',
  templateUrl: './register.component.html',
  styleUrls: ['./register.component.css']
})
export class RegisterComponent implements OnInit {
  profile_info: Profile;
  registerMode = false;
  registerForm: FormGroup;
  @Output() registerToggleSize = new EventEmitter<boolean>();


  constructor(
    private authService: AuthService, 
    private alertify: AlertifyService, 
    private fb: FormBuilder,
    private router: Router
  ) { }

  ngOnInit() {
    this.createRegisterForm();
  }

  createRegisterForm(){
    this.registerForm= this.fb.group({
      first_name: ['', Validators.required],
      last_name: ['', Validators.required],
      zip_code: ['', [ Validators.required, Validators.minLength(5), Validators.maxLength(7), Validators.pattern('[0-9]{5}') ] ],
      email: ['', [ Validators.required, Validators.email ] ],
      password: ['', [Validators.required, Validators.minLength(4), Validators.maxLength(16)]],
      confirmPassword: ['', Validators.required],
    }, {validator: this.passwordMatchValidator})
  }

  passwordMatchValidator( g: FormGroup ){
      return g.get('password').value === g.get('confirmPassword').value ? null : { 'mismatch': true }
  }

  register(){
    if( this.registerForm.valid ){
      this.profile_info = Object.assign( {}, this.registerForm.value );

      this.authService.register(this.profile_info).subscribe(() => {
        this.alertify.success('registration successful');
      }, error => {
        this.alertify.error(error);
      }, () =>{
        this.authService.login(this.profile_info).subscribe(() => {
          this.router.navigate(['/profile/edit'])
        });
      });
    }
  }

  // cancel(){
  //   this.cancelRegister.emit(false);
  // }

  registerToggle(){
    this.registerMode = true;
    this.registerToggleSize.emit(true);
  }

  cancelRegisterMode(registerMode: boolean){
    this.registerMode = registerMode;
    this.registerToggleSize.emit(false);
  }



}
