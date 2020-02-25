import { Component, OnInit } from '@angular/core';
import { AlertifyService } from '../_services/alertify.service';
import { Router } from '@angular/router';
import { AuthService } from '../_services/auth.service';
import { SearchService } from '../_services/search.service';
import { toInt } from 'ngx-bootstrap/chronos/utils/type-checks';

@Component({
  selector: 'app-explore',
  templateUrl: './explore.component.html',
  styleUrls: ['./explore.component.css']
})
export class ExploreComponent implements OnInit {
  projects = [];
  profiles = [];
  model: any = {};
  suggestions: boolean;
  searched: boolean;
  toggleProfile: boolean;
  toggleProject: boolean;

  constructor( 
      private alertify: AlertifyService, 
      private authService: AuthService,
      private searchService: SearchService
    ) { }

  ngOnInit() {
    this.suggestions = this.authService.checkTokenExists() != null ? true : false;
    this.suggestions ? this.getSuggestions() : null;
    this.toggleProfile = false;
    this.toggleProject = false;
  }

  setSearchResults(data){
    console.log(data)
    this.projects = data.projects;
    this.profiles = data.profiles;
  }

  search(){
    this.searchService.search( { 'token': this.authService.checkTokenExists() }, this.model).subscribe(next => {
      console.log(next)
      this.setSearchResults(next);
      this.suggestions = false;
      this.alertify.success('Logged in Successfully');
    }, error => {
      this.alertify.error(error);
    })
  }

  getSuggestions(){
    this.searchService.getSuggestions( { 'token': localStorage.getItem('token') } ).subscribe(next => {
      console.log(next)
      this.setSearchResults(next);
      this.toggleProfile = true;
      this.toggleProject = true;
      this.suggestions = true;
      this.alertify.success('Logged in Successfully');
    }, error => {
      this.alertify.error(error);
    })
  }

  toggleSuggestions(){
    this.ngOnInit();
  }


  toggleProjects(){
    this.toggleProject = !this.toggleProject;
  }


  toggleProfiles(){
    this.toggleProfile = !this.toggleProfile;
  }

}

