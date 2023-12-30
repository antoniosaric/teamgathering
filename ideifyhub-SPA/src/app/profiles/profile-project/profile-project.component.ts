import { Component, OnInit } from '@angular/core';
import { ProfileService } from '../../_services/profile.service';
import { AlertifyService } from '../../_services/alertify.service';
import { Project } from 'src/app/_models/project';
import { ActivatedRoute } from '@angular/router';

@Component({
  selector: 'app-profile-project',
  templateUrl: './profile-project.component.html',
  styleUrls: ['./profile-project.component.css']
})
export class ProfileProjectComponent implements OnInit {
  projects: Project[];

  constructor(
    private profileService: ProfileService, 
    private alertify: AlertifyService,
    private route: ActivatedRoute 
  ) { }

  ngOnInit() {
    this.getProfileProjects();
  }

  getProfileProjects(){
    this.profileService.getProfileProjects({ 'token': localStorage.getItem('token') }).subscribe(response => {
      this.projects = response;
      console.log(response)
    }, error => {
      this.alertify.error(error);
    })
  }

}
