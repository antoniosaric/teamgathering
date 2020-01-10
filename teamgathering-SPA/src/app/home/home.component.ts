import { Component, OnInit } from '@angular/core';
import { AuthService } from '../_services/auth.service';

@Component({
  selector: 'app-home',
  templateUrl: './home.component.html',
  styleUrls: ['./home.component.css']
})
export class HomeComponent implements OnInit {
  projects: any;
  registerMode = false;

  constructor(
    private authService: AuthService, 
  ) { }

  ngOnInit() {

  }


  loggedIn(){
    return this.authService.loggedIn() ? true : false;
  }

  changeRegisterMode(event){
    this.registerMode = event;
  }


}
