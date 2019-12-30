import { Component, OnInit } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { AuthService } from '../_services/auth.service';
import { ProjectService } from '../_services/project.service';
import { AlertifyService } from '../_services/alertify.service';

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
    private projectService: ProjectService, 
    private alertify: AlertifyService
  ) { }

  ngOnInit() {
    this.getHome();
  }

  getHome(){
    this.projectService.getHomepage().subscribe(response => {
      this.projects = response;
    }, error => {
      this.alertify.error(error);
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
