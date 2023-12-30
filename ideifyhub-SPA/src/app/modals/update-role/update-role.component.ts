import { Component, OnInit, EventEmitter, Output, Input } from '@angular/core';
import { TeamService } from '../../_services/team.service';
import { AlertifyService } from '../../_services/alertify.service';
import { AuthService } from '../../_services/auth.service';
import { Team } from 'src/app/_models/team';
import { ActivatedRoute, Router } from '@angular/router';
import { NgForm, Validators, FormBuilder, FormGroup } from '@angular/forms';

@Component({
  selector: 'app-update-role',
  templateUrl: './update-role.component.html',
  styleUrls: ['./update-role.component.css']
})
export class UpdateRoleComponent implements OnInit {
  @Input() profile: any; 
  @Input() team_id: number; 
  @Input() tab: string; 
  @Output() closeModalToggle = new EventEmitter();
  modalState = false; 
  available_status = [ "active" , "inactive" ];
  set_status: string;
  team_profile_update_info: any;

  constructor(
    private alertify: AlertifyService,
    private route: ActivatedRoute, 
    private teamService: TeamService, 
    private authService: AuthService,
    private fb: FormBuilder,
    private router: Router
  ) { }

  ngOnInit() {
    console.log(this.tab)
    this.set_status = this.tab == 'active' ? "active" : 'inactive' ;

  }

  updateRole(form: NgForm){
    this.authService.checkToken();
    this.team_profile_update_info = Object.assign( {}, { ...{'team_id': this.team_id }, ...{'profile_to_change_id': this.profile.profile_id }, ...{ 'profile_team_status': form.value.profile_team_status }, ...{ 'role': form.value.role } } 
    );
    if( form.value.role  == 'owner' ){
      this.alertify.error('cannot change title to owner');
    }else{
      this.teamService.updateRole({ 'token': localStorage.getItem('token') }, this.team_profile_update_info ).subscribe(next => {
        this.authService.setToken(next);
        this.alertify.success('team update successful');
        this.profile.role = form.value.role;
        this.profile.profile_team_status = form.value.profile_team_status;
        form.reset(this.team_profile_update_info);
        this.closeModal();
      }, error => {
        this.alertify.error(error);
      })
    }
  }

  closeModal(){
    this.closeModalToggle.emit();
  }

}
