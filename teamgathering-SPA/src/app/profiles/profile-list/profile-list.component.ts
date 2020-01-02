import { Component, OnInit } from '@angular/core';
import { ProfileService } from '../../_services/profile.service';
import { AlertifyService } from '../../_services/alertify.service';
import { Profile } from 'src/app/_models/profile';

@Component({
  selector: 'app-profile-list',
  templateUrl: './profile-list.component.html',
  styleUrls: ['./profile-list.component.css']
})
export class ProfileListComponent implements OnInit {
  profiles: Profile[];

  constructor(
    private profileService: ProfileService, 
    private alertify: AlertifyService
  ) { }

  ngOnInit() {
    this.getAssociateProfiles();
  }

  getAssociateProfiles(){
    this.profileService.getAssociateProfiles().subscribe(response => {
      this.profiles = response;
    }, error => {
      this.alertify.error(error);
    })
  }

}
