import { Component, OnInit, ViewChild, HostListener, Input } from '@angular/core';
import { ImageCropperModule, ImageCroppedEvent } from 'ngx-image-cropper';
import { AlertifyService } from 'src/app/_services/alertify.service';
import { ImageService } from 'src/app/_services/image.service';
import { NgForm } from '@angular/forms';
import { Profile } from '../_models/profile';
import { AuthService } from '../_services/auth.service';
import { Router } from '@angular/router';

@Component({
  selector: 'app-photos',
  templateUrl: './photos.component.html',
  styleUrls: ['./photos.component.css']
})
export class PhotosComponent implements OnInit {
  @ViewChild('submitImage', {static:true}) submitImage: NgForm;
  cropper:ImageCropperModule;
  fileData: File = null;
  constructor(private alertify: AlertifyService, private imageService: ImageService, private authService: AuthService, private router: Router) { }

  ngOnInit() {}

  imageChangedEvent: any = '';
  croppedImage: any = "https://res.cloudinary.com/dqd4ouqyf/image/upload/v1578555306/profile_"+this.authService.profile_id;

  fileChangeEvent(event: any): void {
      this.imageChangedEvent = event;
  }
  imageCropped(event: ImageCroppedEvent) {
      this.croppedImage = event.base64;
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
      this.submitImage.reset(this.saveProfileImage);
      // this.router.navigate(['/profile/edit/']);
    }, error => {
      this.alertify.error(error);
    })
    
  }

  saveProjectImage($event){
    return $event;
  }


}





