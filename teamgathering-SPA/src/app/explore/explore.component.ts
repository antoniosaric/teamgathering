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
  searches_projects = [];
  searches_profiles = [];
  projects = [];
  profiles = [];
  model_profiles: any = {};
  model_projects: any = {};
  searched_projects: boolean = false;
  searched_profiles: boolean = false;

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
    if( data.projects.length > 0 ){
      this.searches_projects = data.projects;
    }else if( data.profiles.length > 0 ){
      this.searches_profiles = data.profiles;
    }else{
      this.searches_projects = [];
      this.searches_profiles = [];
    }
  }

  setSuggestions(data){
    this.projects = data.projects;
    this.profiles = data.profiles;
  }

  searchProjects(){
    console.log('$$$$$$$$$')
    console.log(this.model_projects)
    this.searched_projects = true;
    this.searched_profiles = false;
    this.searchService.searchProjects( { 'token': this.authService.checkTokenExists() }, this.model_projects).subscribe(next => {
      this.setSearchResults(next);
    }, error => {
      this.alertify.error(error);
    })
  }

  searchProfiles(){
    console.log('%%%%%%%%')
    console.log(this.model_profiles)
    this.searched_projects = false;
    this.searched_profiles = true;
    this.searchService.searchProfiles( { 'token': this.authService.checkTokenExists() }, this.model_profiles).subscribe(next => {
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

