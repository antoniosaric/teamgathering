import { Component, OnInit } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { AuthService } from '../_services/auth.service';
import { AlertifyService } from '../_services/alertify.service';

@Component({
  selector: 'app-home',
  templateUrl: './home.component.html',
  styleUrls: ['./home.component.css']
})
export class HomeComponent implements OnInit {
  projects: any;
  registerMode = false;

  constructor(private http: HttpClient, private authService: AuthService, private alertify: AlertifyService) { }

  ngOnInit() {
    this.getHome();
  }

  getHome(){
    this.http.get('http://localhost:5001/teamtest/teamgathering.API/main/homepage.php').subscribe(response => {
      this.projects = response;
    }, error => {
      this.alertify.error('something went wrong, please reload');
    })
  }

  loggedIn(){
    return this.authService.loggedIn() ? true : false;
  }

  registerToggle(){
    this.registerMode = true;
  }

  cancelRegisterMode(registerMode: boolean){
    this.registerMode = registerMode;
  }

}
