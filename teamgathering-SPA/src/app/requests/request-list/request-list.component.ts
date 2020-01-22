
import { Component, OnInit } from '@angular/core';
import { AuthService } from '../../_services/auth.service';
import { AlertifyService } from '../../_services/alertify.service';
import { RequestService } from '../../_services/request.service';
import { TeamService } from '../../_services/team.service';
import { ActivatedRoute, Router } from '@angular/router';
import { FormGroup, Validators, FormBuilder } from '@angular/forms';

@Component({
  selector: 'app-request-list',
  templateUrl: './request-list.component.html',
  styleUrls: ['./request-list.component.css']
})
export class RequestListComponent implements OnInit {
  updateRequestForm: FormGroup;
  requests_project = [];
  requests_profile = [];
  teams = [];
  request_info: any;
  request_status_selectables = ['approved', 'unapproved'];
  
  constructor(
    private authService: AuthService,
    private alertify: AlertifyService,
    private requestService: RequestService,
    private teamService: TeamService,
    private route: ActivatedRoute,
    private fb: FormBuilder,
    private router: Router
  ) { }

  ngOnInit(){
    this.getRequests();
    this.getTeams();
    this.createRequestForm();
  }

  createRequestForm(){
    this.updateRequestForm = this.fb.group({
      team_id: [ null,  Validators.required ],
      status: [ null, Validators.required  ],
      role: [ '', Validators.required ]
    })
  }

  getRequests(){
    this.requestService.getRequests({ 'token': localStorage.getItem('token') }).subscribe(next => {
      this.requests_profile = next['profile_requests'];
      this.requests_project = next['project_requests'];
    }, error => {
      this.alertify.error(error);
    })
  }

  getTeams(){
    this.teamService.getTeams({ 'token': localStorage.getItem('token') }).subscribe(next => {
      this.teams = next['teams'];
    }, error => {
      this.alertify.error(error);
    })
  }

  updateRequest(data){
    this.authService.checkToken();
    if( this.updateRequestForm.valid ){
      this.request_info = Object.assign( {}, {...this.updateRequestForm.value }, { 'requester_id': data.requester_id },{ 'requestee_id': data.requestee_id },{ 'request_id': data.request_id } );
      console.log(this.request_info);
      this.requestService.updateRequest({ 'token': localStorage.getItem('token') }, this.request_info ).subscribe(next => {
        this.authService.setToken(next);
        this.alertify.success('request successfully made');
        this.updateRequestForm.reset(this.request_info);
      }, error => {
        this.alertify.error(error);
      }, () => {
        this.router.navigate(['/request-list']);
      })
    }
  }

  deleteRequest(data){
    this.authService.checkToken();
    console.log(data)
    if( !!data.request_id ){
      this.requestService.deleteRequest({ 'token': localStorage.getItem('token') }, { 'request_id': data.request_id } ).subscribe(next => {
        this.authService.setToken(next);
        this.alertify.success('request successfully deleted');
      }, error => {
        this.alertify.error(error);
      }, () => {
        this.router.navigate(['/request-list']);
      })
    }
  }

  // requestJoinProject(project_id){
  //   this.requestService.addRequest({ 'token': localStorage.getItem('token') }, {'project_id': project_id}).subscribe(next => {
  //     this.authService.setToken(next);
  //     this.alertify.success('request successfully made');
  //   }, error => {
  //     this.alertify.error(error);
  //   }, () => {

  //   })
  // }

}

