import { Component, OnInit } from '@angular/core';
import { FormControl, NgForm } from '@angular/forms';
import { HttpClient } from '@angular/common/http';
 
import { debounceTime, tap, switchMap, finalize } from 'rxjs/operators';
import { TagService } from 'src/app/_services/tag.service';
import { AuthService } from 'src/app/_services/auth.service';
import { AlertifyService } from 'src/app/_services/alertify.service';
import { Router } from '@angular/router';


@Component({
  selector: 'app-tag',
  templateUrl: './tag.component.html',
  styleUrls: ['./tag.component.css']
})
export class TagComponent implements OnInit {
  tag_object: any;
  searchTagsCtrl = new FormControl();
  filtered_tags: any;
  isLoading = false;
  errorMsg: string;
  tagForSave: any;

  constructor(
    private http: HttpClient, 
    private tagService: TagService,
    private authService: AuthService,
    private alertify: AlertifyService,
    private router: Router
    ) { }

  ngOnInit() {
    
    this.searchTagsCtrl.valueChanges
      .pipe(
        debounceTime(500),
        tap(() => {
          this.errorMsg = "";
          this.filtered_tags = [];
          this.isLoading = true;
        }),
        switchMap(value => {
          if(value != '' && value != null){
            if(value.hasOwnProperty('tag_name')){
              return this.tagService.getTags( { 'token': localStorage.getItem('token') }, { 'tag': value.tag_name } )
              .pipe(
                finalize(() => {
                  this.isLoading = false
                }),
              )
            }else{
              return this.tagService.getTags( { 'token': localStorage.getItem('token') }, { 'tag': value } )
              .pipe(
                finalize(() => {
                  this.isLoading = false
                }),
              )
            }
          }else{
            return [];
          }
        })
      )
      .subscribe(data => {
        if (data['tags'] == undefined ) {
          console.log('one')
          this.errorMsg = data['message'];
          this.tagForSave = {'tag_name': 'something', 'tag_id': 0}
          this.filtered_tags = [];
        } else {
          console.log('two')
          this.tagForSave = {'tag_name': 'xxxxxx', 'tag_id': 0}
          this.errorMsg = "happy";
          this.filtered_tags = data['tags'];
        }
       });
    }

  displayFn(tag) {
    console.log(tag)
    console.log('#######')
    console.log(this.searchTagsCtrl.value)
    if (!tag || tag == undefined || tag == null){
      this.isLoading = false;
      return ''; 
    }else{
      console.log('tag id found')
      this.addTag();
      console.log(tag)
      this.searchTagsCtrl.reset()
    }
  }

  setProfileTag(data){
    console.log(data)

  }

  addTag(){
    this.authService.checkToken();
    if( this.searchTagsCtrl.value != '' && this.searchTagsCtrl.value != null && this.searchTagsCtrl.value != undefined){
      this.tagService.addTag({ 'token': localStorage.getItem('token') }, { 'tag_name': this.searchTagsCtrl.value } ).subscribe(next => {
        this.authService.setToken(next);
        this.alertify.success('skill added successfully');
      }, error => {
        this.alertify.error(error);
      }, () => {
        this.router.navigate(['/profile/edit']);
      })
    }



    console.log('check')
    var something = {"tag_name": this.searchTagsCtrl.value}
    console.log(something)
    console.log('check')
    
    this.searchTagsCtrl.reset()
  }


  onSubmit() {
    console.log('submit')
      console.log(this.searchTagsCtrl.value)
      this.searchTagsCtrl.reset()
    };


}
