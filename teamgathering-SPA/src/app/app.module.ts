import { BrowserModule } from '@angular/platform-browser';
import { NgModule } from '@angular/core';
import { HttpClientModule } from '@angular/common/http';
import { FormsModule } from '@angular/forms';
import { BsDropdownModule } from 'ngx-bootstrap';
import { RouterModule } from '@angular/router';

import { AppRoutingModule } from './app-routing.module';
import { AppComponent } from './app.component';
import { HomeComponent } from './home/home.component';
import { NavComponent } from './nav/nav.component';
import { AuthService } from './_services/auth.service';
import { FooterComponent } from './footer/footer.component';
import { RegisterComponent } from './register/register.component';
import { ErrorInterceptorProvider } from './_services/error.interceptor';
import { RequestsComponent } from './requests/requests.component';
import { MessagesComponent } from './messages/messages.component';
import { ProfileInfoComponent } from './profiles/profile-info/profile-info.component';
import { ProfileListComponent } from './profiles//profile-list/profile-list.component';
import { ProfileEditComponent } from './profiles/profile-edit/profile-edit.component';
import { ProfileCardComponent } from './profiles/profile-card/profile-card.component';
import { ExploreComponent } from './explore/explore.component';
import { TermsandconditionsComponent } from './info/termsandconditions/termsandconditions.component';
import { ContactComponent } from './info/contact/contact.component';
import { PrivacyComponent } from './info/privacy/privacy.component';
import { AboutComponent } from './info/about/about.component';
import { CareersComponent } from './info/careers/careers.component';
import { BlogComponent } from './info/blog/blog.component';
import { PressComponent } from './info/press/press.component';
import { HelpComponent } from './info/help/help.component';
import { BuildingComponent } from './info/building/building.component';


@NgModule({
   declarations: [
      AppComponent,
      HomeComponent,
      NavComponent,
      RegisterComponent,
      FooterComponent,
      RequestsComponent,
      MessagesComponent,
      ProfileEditComponent,
      ProfileListComponent,
      ProfileInfoComponent,
      ProfileCardComponent,
      RequestsComponent,
      ExploreComponent,
      TermsandconditionsComponent,
      ContactComponent,
      PrivacyComponent,
      HelpComponent,
      AboutComponent,
      CareersComponent,
      BlogComponent,
      PressComponent,
      RequestsComponent,
      BuildingComponent
   ],
   imports: [
      BrowserModule,
      AppRoutingModule,
      HttpClientModule,
      FormsModule,
      BsDropdownModule.forRoot(),
      RouterModule
   ],
   providers: [
      AuthService,
      ErrorInterceptorProvider
   ],
   bootstrap: [
      AppComponent
   ]
})
export class AppModule { }
