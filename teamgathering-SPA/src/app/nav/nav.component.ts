import { Component, OnInit } from '@angular/core';
import { AuthService } from '../_services/auth.service';
import { AlertifyService } from '../_services/alertify.service';
import { Router } from '@angular/router';

@Component({
  selector: 'app-nav',
  templateUrl: './nav.component.html',
  styleUrls: ['./nav.component.css']
})
export class NavComponent implements OnInit {
  model: any = {};
  forgotPasswordModalState = false;

  constructor(public authService: AuthService, private alertify: AlertifyService, private router: Router) { }

  ngOnInit() {
  }

  login(){
    this.authService.login(this.model).subscribe(next => {
      this.changeState(false);
      this.alertify.success('Logged in Successfully');
    }, error => {
      this.alertify.error(error);
      this.forgotPasswordModalState = true;
    }, () => {
      this.router.navigate(['/profile-info', this.authService.profile_id]);
    })
  }

  loggedIn(){
    return this.authService.loggedIn();
  }

  logout(){
    this.authService.logout();
  }

  changeState(event){
    this.forgotPasswordModalState = event;
  }

}
