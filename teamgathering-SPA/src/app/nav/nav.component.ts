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

  constructor(public authService: AuthService, private alertify: AlertifyService, private router: Router) { }

  ngOnInit() {
  }

  login(){
    this.authService.login(this.model).subscribe(next => {
      this.alertify.success('Logged in Successfully');
    }, error => {
      this.alertify.error(error);
    }, () => {
      this.router.navigate(['/profile-info', this.authService.profile_id]);
    })
  }

  loggedIn(){
    return this.authService.loggedIn();
  }

logout(){
  localStorage.removeItem('token');
  this.authService.name = '';
  this.authService.profile_id = '';
  this.authService.decodedToken= {};
  this.alertify.success('logged out');
  this.router.navigate(['/home']);
}

}
