import { Component, OnInit } from '@angular/core';
import { HttpClient } from '@angular/common/http';

@Component({
  selector: 'app-home',
  templateUrl: './home.component.html',
  styleUrls: ['./home.component.css']
})
export class HomeComponent implements OnInit {
  projects: any;

  constructor(private http: HttpClient) { }

  ngOnInit() {
    this.getHome();
  }

  getHome(){
    this.http.get('http://localhost:5001/teamtest/teamgathering.API/main/homepage.php').subscribe(response => {
      this.projects = response;
    }, error => {
      console.log(error);
    })
  }
}
