import { Component, OnInit, EventEmitter, Output } from '@angular/core';
import { AuthService } from '../_services/auth.service';
import { AlertifyService } from '../_services/alertify.service';
import { FormGroup, FormControl, Validators, FormBuilder } from '@angular/forms';

@Component({
  selector: 'app-register',
  templateUrl: './register.component.html',
  styleUrls: ['./register.component.css']
})
export class RegisterComponent implements OnInit {
  model: any = {};
  registerMode = false;
  registerForm: FormGroup;
  @Output() registerToggleSize = new EventEmitter<boolean>();


  constructor(private authService: AuthService, private alertify: AlertifyService, private fb: FormBuilder) { }

  ngOnInit() {
    this.createRegisterForm();
    // this.registerForm = new FormGroup({
    //   email: new FormControl('', Validators.required),
    //   password: new FormControl('', [Validators.required, Validators.minLength(4), Validators.maxLength(16)]),
    //   confirmPassword: new FormControl('', Validators.required)
    // }, this.passwordMatchValidator);
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
    console.log(this.registerForm.value);
    // this.authService.register(this.model).subscribe(() => {
    //   this.alertify.success('registration successful');
    // }, error => {
    //   this.alertify.error(error);
    // });
    
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
