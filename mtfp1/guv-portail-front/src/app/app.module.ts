import { BrowserModule } from '@angular/platform-browser';
import { NgModule } from '@angular/core';

import { AppRoutingModule } from './app-routing.module';
import { AppComponent } from './app.component';
import { LoginComponent } from './views/login/login.component';
import { RegisterComponent } from './views/register/register.component';
import {FormsModule} from "@angular/forms";
import { SpinnerLoadingComponent } from './views/spinner-loading/spinner-loading.component';
import { HomeComponent } from './views/home/home.component';
import { HomepfcComponent } from './views/homepfc/homepfc.component';
import {HttpClientModule} from "@angular/common/http";
import { MailCheckComponent } from './views/mail-check/mail-check.component';
import { RegisterSuccessComponent } from './views/register-success/register-success.component';
import { MailCheckResendComponent } from './views/mail-check-resend/mail-check-resend.component';
import { ForgotPasswordComponent } from './views/forgot-password/forgot-password.component';
import { ProfileComponent } from './views/profile/profile.component';
import { LogoutComponent } from './views/logout/logout.component';
import { CheckCodeComponent } from './views/check-code/check-code.component';
import { CarouselModule } from 'ngx-owl-carousel-o';
import { BrowserAnimationsModule } from '@angular/platform-browser/animations';
import { NgbModule } from '@ng-bootstrap/ng-bootstrap';
import { WelcomeComponent } from './welcome/welcome.component';
import { AboutComponent } from './views/about/about.component';
import { InfoPensionComponent } from './views/info-pension/info-pension.component';
import { InfoCarriereComponent } from './views/info-carriere/info-carriere.component';
import { BaseConnaissanceComponent } from './views/base-connaissance/base-connaissance.component';
import { PrestationsParThematiqueComponent } from './views/prestations-par-thematique/prestations-par-thematique.component';
import { PrestationsParStructureComponent } from './views/prestations-par-structure/prestations-par-structure.component';
import { EvenementDeclencheurComponent } from './views/evenement-declencheur/evenement-declencheur.component';
import { FaqComponent } from './views/faq/faq.component';
import { QuestionComponent } from './views/question/question.component';
import { PrendreRendezvousComponent } from './views/prendre-rendezvous/prendre-rendezvous.component';
import { AlloRetraiteComponent } from './views/allo-retraite/allo-retraite.component';
import { JeDenonceComponent } from './views/je-denonce/je-denonce.component';
import { DemandeInformationComponent } from './views/demande-information/demande-information.component';
import { ReclammationComponent } from './views/reclammation/reclammation.component';
import { StructuresComponent } from './views/structures/structures.component';
import { ThematiquesComponent } from './views/thematiques/thematiques.component';
import { PlanComponent } from './views/plan/plan.component';
import { CommonModule } from '@angular/common';
import { MentionsLegalesComponent } from './mentions-legales/mentions-legales.component';
import { LogpfcComponent } from './views/logpfc/logpfc.component'; 
import { ProfilpfcComponent } from './views/profilpfc/profilpfc.component'; 


@NgModule({
  declarations: [
    AppComponent,
    LoginComponent,
    RegisterComponent,
    SpinnerLoadingComponent,
    HomeComponent,
    HomepfcComponent,
    MailCheckComponent,
    RegisterSuccessComponent,
    MailCheckResendComponent,
    ForgotPasswordComponent,
    ProfileComponent,
    LogoutComponent,
    CheckCodeComponent,
    WelcomeComponent,
    AboutComponent,
    InfoPensionComponent,
    InfoCarriereComponent,
    BaseConnaissanceComponent,
    PrestationsParThematiqueComponent,
    PrestationsParStructureComponent,
    EvenementDeclencheurComponent,
    FaqComponent,
    QuestionComponent,
    PrendreRendezvousComponent,
    AlloRetraiteComponent,
    JeDenonceComponent,
    DemandeInformationComponent,
    ReclammationComponent,
    StructuresComponent,
    ThematiquesComponent,
    PlanComponent,
    MentionsLegalesComponent,
    LogpfcComponent,
    ProfilpfcComponent
  ],
  imports: [
    BrowserModule,
    CommonModule,
    CarouselModule,
    BrowserAnimationsModule,
    AppRoutingModule,
    FormsModule,
    HttpClientModule,
    NgbModule,
  ],
  providers: [],
  bootstrap: [AppComponent]
})
export class AppModule { }
