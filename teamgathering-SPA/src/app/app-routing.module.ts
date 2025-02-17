import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { HomeComponent } from './home/home.component';
import { MessagesComponent } from './messages/messages.component';
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
import { FourOFourComponent } from './info/errors/fourOFour/fourOFour.component';
import { ProfileInfoResolver } from './_resolvers/profile-info.resolver';
import { ProjectListResolver } from './_resolvers/project-list.resolver';
import { ProjectInfoResolver } from './_resolvers/project-info.resolver';
import { ProfileListResolver } from './_resolvers/profile-list.resolver';
import { ProfileEditResolver } from './_resolvers/profile-edit.resolver';
import { ProfileEditAccountComponent } from './profiles/profile-edit-account/profile-edit-account.component';
import { PreventUnsavedChanges } from './_guards/prevent-unsaved-changes.guard';
import { ProjectInfoComponent } from './projects/project-info/project-info.component';
import { ProjectEditComponent } from './projects/project-edit/project-edit.component';
import { ProjectEditResolver } from './_resolvers/project-edit.resolver';
import { ProjectAddComponent } from './projects/project-add/project-add.component';
import { RequestListComponent } from './requests/request-list/request-list.component';
import { TeamEditComponent } from './teams/team-edit/team-edit.component';
import { TeamAddComponent } from './teams/team-add/team-add.component';
import { TeamEditResolver } from './_resolvers/team-edit.resolver';
import { ProfileProjectComponent } from './profiles/profile-project/profile-project.component';
import { SuggestionsComponent } from './suggestions/suggestions.component';



const routes: Routes = [
  { path: '', component: HomeComponent, resolve: {projects: ProjectListResolver} },
  { path: 'explore', component: ExploreComponent },
  { path: 'suggestions', component: SuggestionsComponent },
  {
    path: '',
    runGuardsAndResolvers: 'always',
    canActivate: [AuthGuard],
    children: [
      // { path: 'profiles', component: ProfileListComponent, resolve: {users: ProfileListResolver} },
      { path: 'messages', component: MessagesComponent, canActivate: [AuthGuard] },
      { path: 'request-list', component: RequestListComponent, canActivate: [AuthGuard] },
      { path: 'profile/edit', component: ProfileEditComponent, resolve: {profile: ProfileEditResolver}, canActivate: [AuthGuard], canDeactivate: [PreventUnsavedChanges] },
      { path: 'profile/edit-account', component: ProfileEditAccountComponent, canActivate: [AuthGuard] },
      { path: 'profile-list', component: ProfileListComponent, resolve: {profiles: ProfileListResolver}, canActivate: [AuthGuard] },
      { path: 'project/edit/:id', component: ProjectEditComponent, resolve: {project: ProjectEditResolver}, canActivate: [AuthGuard] },
      { path: 'team/edit/:id', component: TeamEditComponent, resolve: {team: TeamEditResolver}, canActivate: [AuthGuard] },
      { path: 'profile-project', component: ProfileProjectComponent, canActivate: [AuthGuard] },
      { path: 'team/add', component: TeamAddComponent, canActivate: [AuthGuard] },
      { path: 'project/add', component: ProjectAddComponent, canActivate: [AuthGuard] }

    ]
  },
  {
    path: '',
    runGuardsAndResolvers: 'always',
    children: [
      { path: 'project-info/:id', component: ProjectInfoComponent, resolve: {project: ProjectInfoResolver} },
      { path: 'profile-info/:id', component: ProfileInfoComponent, resolve: {profile: ProfileInfoResolver} },
      
    ]
  },
  // { path: 'messages', component: MessagesComponent, canActivate: [AuthGuard] },
  // { path: 'profile', component: ProfileDetailComponent, canActivate: [AuthGuard] },
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
  imports: [RouterModule.forRoot(routes, {useHash: false})],
  exports: [RouterModule]
})
export class AppRoutingModule { }
