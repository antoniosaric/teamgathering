import { Component, OnInit } from '@angular/core';
import { AlertifyService } from '../../_services/alertify.service';
import { ProfileService } from '../../_services/profile.service';
import { ActivatedRoute, Router } from '@angular/router';


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
    private router: Router
  ) { }

  ngOnInit() {
    this.getProfile();
  }

  getProfile(){

    const params = {'profile_id': this.route.snapshot.paramMap.get('id')};

    this.profileService.getProfile(params).subscribe(response => {
        this.profile_info = response;
    }, error => {
      (error == 'page not found') ? this.router.navigate(['/404']) : null ;
      this.alertify.error(error);
    }, () => {
    })
  }


}
