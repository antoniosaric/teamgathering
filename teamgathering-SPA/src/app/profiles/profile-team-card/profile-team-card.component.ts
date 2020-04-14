import { Component, OnInit, Input } from '@angular/core';
import { Profile } from 'src/app/_models/profile';

@Component({
  selector: 'app-profile-team-card',
  templateUrl: './profile-team-card.component.html',
  styleUrls: ['./profile-team-card.component.css']
})
export class ProfileTeamCardComponent implements OnInit {
  @Input() profile: Profile;

  constructor() { }

  ngOnInit() {
    console.log(this.profile)
  }

  toPresentCheck(date){
    var current_date = new Date();
    if( date == '0000-00-00 00:00:00' || current_date == date || date == undefined ){
      return true;
    }else{
      return false;
    }
  }

}
