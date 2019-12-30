import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { HomeComponent } from './home/home.component';
import { MessagesComponent } from './messages/messages.component';
import { RequestsComponent } from './requests/requests.component';
import { ProfileListComponent } from './profiles/profile-list/profile-list.component';
import { ProfileEditComponent } from './profiles/profile-edit/profile-edit.component';
import { ProfileInfoComponent } from './profiles/profile-info/profile-info.component';
import { TermsandconditionsComponent } from './info/termsandconditions/termsandconditions.component';
import { ContactComponent } from './info/contact/contact.component';
import { PrivacyComponent } from './info/privacy/privacy.component';
import { HelpComponent } from './info/help/help.component';
import { ExploreComponent } from './explore/explore.component';
import { AboutComponent } from './info/about/about.component';
import { CareersComponent } from './info/careers/careers.component';
import { BlogComponent } from './info/blog/blog.component';
import { PressComponent } from './info/press/press.component';
import { AuthGuard } from './_guards/auth.guard';
import { FourOFourComponent } from './info/fourOFour/fourOFour.component';



const routes: Routes = [
  { path: '', component: HomeComponent },
  { path: 'explore', component: ExploreComponent },
  {
    path: '',
    runGuardsAndResolvers: 'always',
    canActivate: [AuthGuard],
    children: [
      // { path: 'projects/:id', component: ProjectDetailComponent, resolve: {project: ProjectDetailResolver} },
      // { path: 'profiles', component: ProfileListComponent, resolve: {users: ProfileListResolver} },
      { path: 'messages', component: MessagesComponent, canActivate: [AuthGuard] },
      { path: 'requests', component: RequestsComponent, canActivate: [AuthGuard] },
      { path: 'profile-info/:id', component: ProfileInfoComponent },
      // { path: 'profile/edit', component: ProfileEditComponent, resolve: {user: ProfileEditResolver}, canDeactivate: [PreventUnsavedChanges] },
      { path: 'profile/edit', component: ProfileEditComponent },
      // { path: 'profile-project', component: ProfileProjectListComponent },
      // { path: 'project-list', component: ProjectListComponent },
      { path: 'profile-list', component: ProfileListComponent }
    ]
  },
  // { path: 'messages', component: MessagesComponent, canActivate: [AuthGuard] },
  // { path: 'profile', component: ProfileDetailComponent, canActivate: [AuthGuard] },
  // { path: 'profile-project', component: ProfileProjectListComponent, canActivate: [AuthGuard] },
  { path: 'termsandconditions', component: TermsandconditionsComponent },
  { path: 'privacy', component: PrivacyComponent },
  { path: 'contact', component: ContactComponent },
  { path: 'help', component: HelpComponent },
  { path: 'about', component: AboutComponent },
  { path: 'careers', component: CareersComponent },
  { path: 'blog', component: BlogComponent },
  { path: 'press', component: PressComponent },
  { path: '404', component: FourOFourComponent },
  { path: '**', redirectTo: '', pathMatch: 'full' },
];

@NgModule({
  imports: [RouterModule.forRoot(routes)],
  exports: [RouterModule]
})
export class AppRoutingModule { }
