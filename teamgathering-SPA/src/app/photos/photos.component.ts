import { Component, OnInit, ViewChild, HostListener, Input, Output, EventEmitter } from '@angular/core';
import { ImageCropperModule, ImageCroppedEvent } from 'ngx-image-cropper';
import { AlertifyService } from 'src/app/_services/alertify.service';
import { ImageService } from 'src/app/_services/image.service';
import { NgForm } from '@angular/forms';
import { AuthService } from '../_services/auth.service';
import { Router } from '@angular/router';

@Component({
  selector: 'app-photos',
  templateUrl: './photos.component.html',
  styleUrls: ['./photos.component.css']
})
export class PhotosComponent implements OnInit {
  @ViewChild('submitImage', {static:true}) submitImage: NgForm;
  @Input() image: string; 
  @Input() page: string; 
  @Input() project_id: string; 
  @Output() onPhotoSaveSetPhoto = new EventEmitter<object>();
  @Output() onPhotoSaveSetState = new EventEmitter<string>();
  imageChangedEvent: any = '';
  croppedImage: any = '';
  cropper:ImageCropperModule;
  fileData: File = null;
  imageSubmitted: boolean = false;

  constructor(
    private alertify: AlertifyService, 
    private imageService: ImageService, 
    private authService: AuthService, 
    private router: Router
  ) { }

  ngOnInit() {
    this.croppedImage = this.image;
  }

 

  fileChangeEvent(event: any): void {
      this.imageChangedEvent = event;
  }
  imageCropped(event: ImageCroppedEvent) {
      this.croppedImage = event.base64;
      this.imageSubmitted = true;
  }
  imageLoaded() {
      // show cropper
  }
  cropperReady() {
      // cropper ready
  }
  loadImageFailed() {
    this.alertify.error('image cannot be submitted');
  }

  saveProfileImage(event: any){
    this.imageService.saveProfileImage({ 'token': localStorage.getItem('token') }, { 'image': this.croppedImage } ).subscribe(next => {
      this.alertify.success('imaged update successful');
      this.onPhotoSaveSetPhoto.emit(next);
      this.onPhotoSaveSetState.emit('profile');
      this.imageSubmitted = false;
    }, error => {
      this.alertify.error(error);
    })
  }

  saveProjectImage($event){
    this.imageService.saveProjectImage({ 'token': localStorage.getItem('token') }, { 'image': this.croppedImage }, {'project_id': this.project_id}).subscribe(next => {
      this.alertify.success('imaged update successful');
      this.onPhotoSaveSetPhoto.emit(next);
      this.onPhotoSaveSetState.emit('project');
      this.imageSubmitted = false;
    }, error => {
      this.alertify.error(error);
    })
  }

  deleteProfileImage(){
    this.alertify.confirm('Are you suer you want to delete your photo?', () => {
      this.imageService.deleteProfileImage({'token': localStorage.getItem('token') } ).subscribe(next => {
        this.alertify.success('imaged deleted');
        this.onPhotoSaveSetPhoto.emit(next);
        this.onPhotoSaveSetState.emit('profile');
        this.imageSubmitted = false;
      }, error => {
        this.alertify.error('failed to delete the photo');
      })
    })
  }

  deleteProjectImage(project_id: number){
    this.alertify.confirm('Are you suer you want to delete your photo?', () => {
      this.imageService.deleteProjectImage({'token': localStorage.getItem('token') }, { 'project_id': this.project_id } ).subscribe(next => {
        this.alertify.success('imaged deleted');
        this.onPhotoSaveSetPhoto.emit(next);
        this.onPhotoSaveSetState.emit('project');
        this.imageSubmitted = false;
      }, error => {
        this.alertify.error('failed to delete the photo');
      })
    })
  }

  goBackProfile(){
    this.onPhotoSaveSetState.emit('profile');
  }

  goBackProject(){
    this.onPhotoSaveSetState.emit('project');
  }

}





