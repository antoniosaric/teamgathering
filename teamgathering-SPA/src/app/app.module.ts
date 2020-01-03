import { BrowserModule } from '@angular/platform-browser';
import { NgModule } from '@angular/core';
import { HttpClientModule } from '@angular/common/http';
import { FormsModule } from '@angular/forms';
import { BsDropdownModule, TabsModule } from 'ngx-bootstrap';
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
import { ProfileListComponent } from './profiles/profile-list/profile-list.component';
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
import { FourOFourComponent } from './info/fourOFour/fourOFour.component';
import { ProjectCardComponent } from './projects/project-card/project-card.component';
import { ProjectInfoComponent } from './projects/project-info/project-info.component';
import { ProjectListComponent } from './projects/project-list/project-list.component';
import { ProjectEditComponent } from './projects/project-edit/project-edit.component';
import { AlertifyService } from './_services/alertify.service';
import { AuthGuard } from './_guards/auth.guard';
import { ProfileService } from './_services/profile.service';
import { ProjectListResolver } from './_resolvers/project-list.resolver';
import { ProfileInfoResolver } from './_resolvers/profile-info.resolver';
import { ProfileListResolver } from './_resolvers/profile-list.resolver';
import { ProfileEditResolver } from './_resolvers/profile-edit.resolver';

// export function tokenGetter() {
//    return localStorage.getItem("token");
//  }

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
      ProjectCardComponent,
      ProjectInfoComponent,
      ProjectListComponent,
      ProjectEditComponent,
      RequestsComponent,
      ExploreComponent,
      TermsandconditionsComponent,
      ContactComponent,
      PrivacyComponent,
      FourOFourComponent,
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
      TabsModule.forRoot(),
      RouterModule,
      // JwtModule.forRoot({
      //    config:{
      //       tokenGetter: tokenGetter,
      //       whitelistedDomains: ['localhost:5001'],
      //       blacklistedRoutes: ['localhost:5001/teamtest/teamgathering.API/_authorization/do_login.php']
      //    }
      // }),
   ],
   providers: [
      AuthService,
      ErrorInterceptorProvider, 
      AlertifyService,
      AuthGuard,
      ProjectListResolver,
      ProfileService,
      ProfileInfoResolver,
      ProfileListResolver,
      ProfileEditResolver
   ],
   bootstrap: [
      AppComponent
   ]
})
export class AppModule { }
