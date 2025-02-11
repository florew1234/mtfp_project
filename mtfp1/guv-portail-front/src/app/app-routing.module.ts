import { NgModule } from '@angular/core';
import { Routes, RouterModule, ExtraOptions } from '@angular/router';
import {LoginComponent} from "./views/login/login.component";
import {RegisterComponent} from "./views/register/register.component";
import {HomeComponent} from "./views/home/home.component";
import {HomepfcComponent} from "./views/homepfc/homepfc.component";
import {AuthGuard} from './core/_guards/auth.guard';
import {AuthGuardm} from './core/_guards/authm.guard';
import {IsAuthGuard} from './core/_guards/is-auth.guard';
import {RegisterSuccessComponent} from './views/register-success/register-success.component';
import {RegisterguardGuard} from './core/_guards/registerguard.guard';
import {MailCheckComponent} from './views/mail-check/mail-check.component';
import {ForgotPasswordComponent} from './views/forgot-password/forgot-password.component';
import {ProfileComponent} from './views/profile/profile.component';
import {LogoutComponent} from './views/logout/logout.component';
import {CheckCodeComponent} from './views/check-code/check-code.component';
import { WelcomeComponent } from './welcome/welcome.component';
import { AboutComponent } from './views/about/about.component';
import { BaseConnaissanceComponent } from './views/base-connaissance/base-connaissance.component';
import { InfoCarriereComponent } from './views/info-carriere/info-carriere.component';
import { InfoPensionComponent } from './views/info-pension/info-pension.component';

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
import { MentionsLegalesComponent } from './mentions-legales/mentions-legales.component';
import { LogpfcComponent } from './views/logpfc/logpfc.component';
import { ProfilpfcComponent } from './views/profilpfc/profilpfc.component';

const routes: Routes = [
    /*{
      component: LoginComponent,
        path: "login",
        canActivate: [IsAuthGuard]
    },*/
    {
        component: WelcomeComponent,
          path: "main",
          canActivate: [IsAuthGuard]
        
    },
    {
        component: PrestationsParThematiqueComponent,
          path: "prestations-par-thematique",
          canActivate: [IsAuthGuard]
        
    },
    {
        component: PrestationsParStructureComponent,
          path: "prestations-par-structure",
          canActivate: [IsAuthGuard]
        
    },
    {
        component: EvenementDeclencheurComponent,
          path: "evenements-declencheur",
          canActivate: [IsAuthGuard]
        
    },
    {
        component: FaqComponent,
          path: "faq",
          canActivate: [IsAuthGuard]
        
    },
    {
        component: QuestionComponent,
          path: "question",
          canActivate: [IsAuthGuard]
        
    },
    {
        component: PrendreRendezvousComponent,
          path: "prendre-rendezvous",
          canActivate: [IsAuthGuard]
        
    },
    {
        component: AlloRetraiteComponent,
          path: "allo-retraite",
          canActivate: [IsAuthGuard]
        
    },
    {
        component: PlanComponent,
          path: "plan",
          canActivate: [IsAuthGuard]
        
    },
   /* {
        component: ThematiquesComponent,
          path: "thematiques"
        
    },
    {
        component: StructuresComponent,
          path: "structures"
        
    },*/
    {
        component: ReclammationComponent,
          path: "reclammation",
          canActivate: [IsAuthGuard]
        
    },
    {
        component: DemandeInformationComponent,
          path: "demande-info",
          canActivate: [IsAuthGuard]
        
    },
    {
        component: JeDenonceComponent,
          path: "je-denonce",
          canActivate: [IsAuthGuard]
        
    },
    {
        component: AboutComponent,
          path: "about",
          canActivate: [IsAuthGuard]
        
    },
    {
        component: BaseConnaissanceComponent,
          path: "base-connaissance"
        
    },
   /* {
        component: InfoCarriereComponent,
          path: "info-carriere"
        
    },
    {
        component: InfoPensionComponent,
          path: "info-pension"
        
    },*/
    {
        component: ForgotPasswordComponent,
        path: "forgot-password",
    }, {
        component: ProfileComponent,
        path: "profile",
        canActivate: [AuthGuard]
    },{
        component: CheckCodeComponent,
        path: "check-code",
    },
    {
        component: LogoutComponent,
        path: "logout",
    },
    {
        component: MentionsLegalesComponent,
        path: "mentions-legales",
    },
    {
        component: RegisterComponent,
        path:"register",
        canActivate: [IsAuthGuard]
    },
    {
        component: HomeComponent,
        path:"home",
        canActivate: [AuthGuard]
    },
    {
        component: HomepfcComponent,
        path:"homepfc",
        canActivate: [AuthGuardm]
    },
    {
        component: LogpfcComponent,
        path:"logpfc",
        // canActivate: [AuthGuard]
    },
    {
        component: ProfilpfcComponent,
        path:"profilepfc",
        // canActivate: [AuthGuard]
    },
    {
        component: RegisterSuccessComponent,
        path:"register-success",
        canActivate:[RegisterguardGuard]
    },
    {
        component: RegisterSuccessComponent,
        path:"mail-check-code-resent",
        canActivate:[RegisterguardGuard]
    },
    {
        path:"",
        pathMatch:"full",
        redirectTo:"main"
    },
];

const routerOptions: ExtraOptions = {
    useHash: false,
    // ...any other options you'd like to use
  };
  
@NgModule({
  imports: [RouterModule.forRoot(routes,routerOptions)],
  exports: [RouterModule]
})
export class AppRoutingModule { }
