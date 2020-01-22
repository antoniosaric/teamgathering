import { Component, OnInit } from '@angular/core';
import { AlertifyService } from '../../_services/alertify.service';
import { ProfileService } from '../../_services/profile.service';
import { ActivatedRoute, Router } from '@angular/router';
import { AuthService } from 'src/app/_services/auth.service';


@Component({
  selector: 'app-profile-info',
  templateUrl: './profile-info.component.html',
  styleUrls: ['./profile-info.component.css']
})
export class ProfileInfoComponent implements OnInit {
  profile_info: any;

  constructor(
    private alertify: AlertifyService, 
    private profileService: ProfileService,
    private route: ActivatedRoute,
    private router: Router,
    public authService: AuthService
  ) { }

  ngOnInit() {
    this.route.data.subscribe(data => {
      this.profile_info = data['profile'];
    })
  }



  
}
