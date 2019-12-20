import { Component, OnInit } from '@angular/core';
import { logging } from 'protractor';
import { AuthService } from '../_services/auth.service';

@Component({
  selector: 'app-nav',
  templateUrl: './nav.component.html',
  styleUrls: ['./nav.component.css']
})
export class NavComponent implements OnInit {
  model: any = {};

  constructor(private authService: AuthService) { }

  ngOnInit() {
  }

  login(){
    this.authService.login(this.model).subscribe(next => {
      console.log('logged in');
      // this.alertify.success('Logged in Successfully');
    }, error => {
      // this.alertify.error(error);
    }, () => {
      // this.router.navigate(['/home']);
    })
  }

  loggedIn(){
    return this.authService.loggedIn();
  }

logout(){
  localStorage.removeItem('token');
  console.log('logged out');
  // this.alertify.success('logged out');
  // this.router.navigate(['/home']);
}

}
