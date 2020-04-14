import { Component, OnInit } from '@angular/core';
import { AlertifyService } from '../_services/alertify.service';
import { Router } from '@angular/router';
import { AuthService } from '../_services/auth.service';
import { SearchService } from '../_services/search.service';
import { toInt } from 'ngx-bootstrap/chronos/utils/type-checks';
import { StatusService } from '../_services/status.service';

@Component({
  selector: 'app-explore',
  templateUrl: './explore.component.html',
  styleUrls: ['./explore.component.css']
})
export class ExploreComponent implements OnInit {
  searches = [];
  projects = [];
  profiles = [];
  model: any = {};

  constructor( 
      private alertify: AlertifyService, 
      private authService: AuthService,
      private searchService: SearchService,
      private statusService: StatusService
    ) { }

  ngOnInit() {
    this.authService.checkTokenExists() != null ? this.getSuggestions() : false;
    this.statusService.searchStatus();
  }

  setSearchResults(data){
    this.searches = data.projects;
  }

  setSuggestions(data){
    this.projects = data.projects;
    this.profiles = data.profiles;
  }

  search(){
    this.searchService.search( { 'token': this.authService.checkTokenExists() }, this.model).subscribe(next => {
      this.setSearchResults(next);
    }, error => {
      this.alertify.error(error);
    })
  }

  getSuggestions(){
    this.searchService.getSuggestions( { 'token': localStorage.getItem('token') } ).subscribe(next => {
      console.log(next)
      this.setSuggestions(next);
    }, error => {
      this.alertify.error(error);
    })
  }


}

