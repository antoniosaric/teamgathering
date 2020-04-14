import { Component, OnInit } from '@angular/core';
import { ProfileService } from '../../_services/profile.service';
import { AlertifyService } from '../../_services/alertify.service';
import { Profile } from 'src/app/_models/profile';
import { ActivatedRoute } from '@angular/router';
import { StatusService } from 'src/app/_services/status.service';

@Component({
  selector: 'app-profile-list',
  templateUrl: './profile-list.component.html',
  styleUrls: ['./profile-list.component.css']
})
export class ProfileListComponent implements OnInit {
  profiles: Profile[];
  teams_array = [];

  constructor(
    private profileService: ProfileService, 
    private alertify: AlertifyService,
    private route: ActivatedRoute,
    private statusService: StatusService
  ) { }

  ngOnInit() {
    this.route.data.subscribe(data => {
      this.profiles = data['profiles'];
    })

    this.setTeamArray();
    this.statusService.searchStatus();
    console.log(this.profiles)
    // this.getAssociateProfiles();
  }

  // getAssociateProfiles(){
  //   this.profileService.getAssociateProfiles().subscribe(response => {
  //     this.profiles = response;
  //   }, error => {
  //     this.alertify.error(error);
  //   })
  // }

  setTeamArray(){
    for(var i = 0 ; i < this.profiles.length ; i++){
      var team_object = { 'team_name': this.profiles[i].team_name, 'team_id': this.profiles[i].team_id, 'project_name': this.profiles[i].project_name, 'project_id': this.profiles[i].project_id };
      if ( !this.teams_array.some(e => e.team_id === team_object.team_id)) {
        this.teams_array.push( team_object ) ;
      }
    }





    // for(var i = 0 ; i < this.profiles.length ; i++){
    //   var team_object = { 'team_name': this.profiles[i].team_name, 'team_id': this.profiles[i].team_id, 'project_name': this.profiles[i].project_name, 'project_id': this.profiles[i].project_id };
    //   if( this.teams_array.includes( team_object ) ){
    //   }else{
    //     this.teams_array.push( team_object ) ;
    //   }
    // }
  }

  // teamIdCheck(team_id){
  //   if( this.teams_array.includes( team_id ) ){
  //     this.teams_array.push( team_id ) ;
  //     return true;
  //   }else{
  //     this.teams_array.push( team_id ) ;
  //     return false;
  //   }
  // }



}
