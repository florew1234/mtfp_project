import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';

import { ProfilComponent } from './views/pages/profil/profil.component';
import { BaseComponent } from './views/pages/base/base.component';
import { DashboardComponent } from './views/pages/dashboard/dashboard.component';
import { Error403Component } from './views/pages/error403/error403.component';
import { LoginComponent } from './views/pages/auth/login/login.component';
import { ForgotPasswordComponent } from './views/pages/auth/forgot-password/forgot-password.component';
import { ResetPasswordComponent } from './views/pages/auth/reset-password/reset-password.component';
import { AuthRoleGuard } from './core/_guards/auth-role.guard';
import { AuthUsagerGuard } from './core/_guards/auth-usager.guard';


import { CrudComponent } from './views/pages/crud/crud.component';
import { ListeprofilsComponent } from './views/pages/parameters/listeprofils/listeprofils.component';
import { ListetapesComponent } from './views/pages/parameters/listetapes/listetapes.component';
import { RapCommentComponent } from './views/pages/others/list-rap-comment/listrapcom.component';
import { ListenatureComponent } from './views/pages/parameters/listenature/listenature.component';
import { ListestructuresComponent } from './views/pages/parameters/listestructures/listestructures.component';
import { ListtypeComponent } from './views/pages/parameters/listtype/listtype.component';
import { EventsComponent } from './views/pages/parameters/events/events.component';
import { ListacteursComponent } from './views/pages/parameters/listacteurs/listacteurs.component';
import { ListerdvcrenauxComponent } from './views/pages/parameters/listerdvcrenaux/listerdvcrenaux.component';
import { ListdaterdvComponent } from './views/pages/parameters/listdaterdv/listdaterdv.component';
import { ListerdvparametreComponent } from './views/pages/parameters/listerdvparametre/listerdvparametre.component';
import { UsersComponent } from './views/pages/users/users.component';
import { ListusagerComponent } from './views/pages/parameters/listusager/listusager.component';
import { ListRequeteUpdateComponent } from './views/pages/others/list-requete-update/list-requete-update.component';
import { ListRequeteStructuresComponent } from './views/pages/others/list-requete-structures/list-requete-structures.component';
import { ListRequeteServicesComponent } from './views/pages/others/list-requete-services/list-requete-services.component';
import { ListRequeteDivisionComponent } from './views/pages/others/list-requete-division/list-requete-division.component';
import { ListRequeteUsagerComponent } from "./views/pages/others/list-requete-usager/list-requete-usager.component";
import { ParcoursRequeteComponent } from './views/pages/others/parcours-requete/parcours-requete.component';
import { ParcoursRegistreComponent } from './views/pages/others/parcours-registre/parcours-registre.component';
import { StatspreocComponent } from './views/pages/others/stats-preoc/stats-preoc.component';
import { PointReponseComponent } from './views/pages/others/point-reponse/point-reponse.component';
import { ListRdvComponent } from './views/pages/others/list-rdv/list-rdv.component';
import { ListStatPrestationComponent } from './views/pages/others/list-stat-prestation/list-stat-prestation.component';
import { ListStatThemeComponent } from './views/pages/others/list-stat-theme/list-stat-theme.component';
import { ListStatStructureComponent } from './views/pages/others/list-stat-structure/list-stat-structure.component';
import { ListauxDigitComponent } from './views/pages/others/list-taux-digita/list-taux-digita.component';
import { RequetesComponent } from './views/pages/others/requetes/requetes.component';
import { LoginUsagerComponent } from './views/pages/auth/login-usager/login-usager.component';
import { EspaceusagerComponent } from './views/pages/others/espaceusager/espaceusager.component';
import { ListeserviceComponent } from './views/pages/parameters/listeservice/listeservice.component';
import { AttributcomComponent } from './views/pages/parameters/attributcom/attributcom.component';
import { GraphiqueevolutionComponent } from './views/pages/others/graphiqueevolution/graphiqueevolution.component';
import { GraphiquetypeComponent } from './views/pages/others/graphiquetype/graphiquetype.component';
import { GraphiquestructureComponent } from './views/pages/others/graphiquestructure/graphiquestructure.component';
import { ListRatioPlaintePrestaionComponent } from './views/pages/others/list-ratio-plainte-prestaion/list-ratio-plainte-prestaion.component';
import { ListRatioRquetePrestaionComponent } from './views/pages/others/list-ratio-rquete-prestaion/list-ratio-rquete-prestaion.component';
import { ListRatioPlainteStructureComponent } from './views/pages/others/list-ratio-plainte-structure/list-ratio-plainte-structure.component';
import { ListRatioRequeteStructureComponent } from './views/pages/others/list-ratio-requete-structure/list-ratio-requete-structure.component';
import { ListRequeteAdjointComponent } from './views/pages/others/list-requete-adjoint/list-requete-adjoint.component';
import { BaseUsagerComponent } from './views/pages/base-usager/base-usager.component';
import { ListRatioDemandeInfosStructureComponent } from './views/pages/others/list-ratio-demande-infos-structure/list-ratio-demande-infos-structure.component';
import { ListRatioDemandeInfosPrestationComponent } from './views/pages/others/list-ratio-demande-infos-prestation/list-ratio-demande-infos-prestation.component';
import { IsAuthGuard } from './core/_guards/is-auth.guard';
import { RelanceComponent } from './views/pages/others/relance/relance.component';
import { ListeinstitutionComponent } from './views/pages/parameters/listeinstitution/listeinstitution.component';
import { ConfigrelanceComponent } from './views/pages/parameters/configrelance/configrelance.component';
import { UsersMainComponent } from './views/pages/users-main/users-main.component';
import { ListDenonciationComponent } from './views/pages/others/list-denonciation/list-denonciation.component';
import { ListSuggestionComponent } from './views/pages/others/list-suggestion/list-suggestion.component';
import { GuideComponent } from './views/pages/others/guide/guide.component';
import { ListStatPrestationStructureComponent } from './views/pages/list-stat-prestation-structure/list-stat-prestation-structure.component';
import { ListserviceatraiterComponent } from './views/pages/parameters/listserviceatraiter/listserviceatraiter.component';
import { AllServicesComponent } from './views/pages/all-services/all-services.component';
import { PointPreoccupationComponent } from './views/pages/others/point-preoccupation/point-preoccupation.component';
import { ManageRequeteUsagerComponent } from './views/pages/others/manage-requete-usager/manage-requete-usager.component';
import { ComplementInformationComponent } from './views/pages/others/complement-information/complement-information.component';
import { LoginV2Component } from './views/pages/auth/login-v2/login-v2.component';
import { LoginCheckComponent } from './views/pages/auth/login-check/login-check.component';
import { EspacepointfocalcomComponent } from './views/pages/others/espacepointfocalcom/espacepointfocalcom.component';
import { SettingsComponent } from './views/pages/settings/settings.component';
import { EservicesComponent } from './views/pages/eservices/eservices.component';
import { CcspComponent } from './views/pages/ccsp/ccsp.component';
import { TypeStructureComponent } from './views/pages/type-structure/type-structure.component';
import { NatureContractComponent } from './views/pages/nature-contract/nature-contract.component';
import { AvancedStatisticsComponent } from './views/pages/avanced-statistics/avanced-statistics.component';


const routes: Routes = [
  {
    path:"login",
    component:LoginComponent,
    //canActivate:[IsAuthGuard]
  },
  {
    path:"login-check/:email",
    component:LoginCheckComponent
  },
  {
    path:"login-usager",
    component:LoginUsagerComponent
  },
  {
    path:"login-v2",
    component:LoginV2Component
  },
  {
    path:"login-v2/:code",
    component:LoginV2Component
  },
  {
    path:"reset-password/:token",
    component:ResetPasswordComponent
  },
  {
    path:"forgot-password",
    component:ForgotPasswordComponent
  },
  {
    path: 'requete-usager/complement-information/:id/:codeRequete',
    component:ComplementInformationComponent
  },
  {
    path:"login/:lang",
    component:LoginComponent
  },
  {
    path:"reset-password/:lang/:token",
    component:ResetPasswordComponent
  },
  {
    path:"forgot-password/:lang",
    component:ForgotPasswordComponent
  },
  {
    path: 'usager',
    component: BaseUsagerComponent,
    // canActivate: [AuthUsagerGuard],
    children: [
      {
        path: 'espace',
        component:EspaceusagerComponent,
        //canActivate:[AuthUsagerGuard] 
      },
    ]
  },
  {
    path: '',
    component: BaseComponent,
    // canActivate: [AuthRoleGuard],
    children: [
    
      {
        path: 'crud',
        component:CrudComponent
      },
      {
        path: 'listprofils',
        component:ListeprofilsComponent
      },
      {
        path: 'pointfocalcom',
        component:EspacepointfocalcomComponent
      },
      {
        path: 'listservices',
        component:ListeserviceComponent
      },
      {
        path: 'attributcom',
        component:AttributcomComponent
      },
      {
        path: 'listservicesatraiter',
        component:ListserviceatraiterComponent
      },
      {
        path: 'allservices',
        component:AllServicesComponent
      },
      {
        path: 'avanced-statistics',
        component:AvancedStatisticsComponent
      },
      {
        path: 'managerequeteusager',
        component:ManageRequeteUsagerComponent
      },
    
      {
        path: 'listdenonciations',
        component:ListDenonciationComponent
      },
      {
        path: 'listsuggestions',
        component:ListSuggestionComponent
      },
      {
        path: 'docs',
        component:GuideComponent
      },
      {
        path: 'liststructure',
        component:ListestructuresComponent
      },
      {
        path: 'listcreneaux',
        component:ListerdvcrenauxComponent
      },
      {
        path: 'rdvparam',
        component:ListerdvparametreComponent
      },
      {
        path: 'listdaterdv',
        component:ListdaterdvComponent
      },
      {
        path: 'users',
        component:UsersComponent
      },
      {
        path: 'e-services',
        component:EservicesComponent
      },
      {
        path: 'type-structures',
        component:TypeStructureComponent
      },
      {
        path: 'nature-contracts',
        component:NatureContractComponent
      },
      {
        path: 'ccsps',
        component:CcspComponent
      },
      {
        path: 'settings',
        component:SettingsComponent
      },
      {
        path: 'users-main',
        component:UsersMainComponent
      },
      {
        path: 'listusager',
        component:ListusagerComponent
      },
      {
        path: 'listacteur',
        component:ListacteursComponent
      },
      {
        path: 'events',
        component:EventsComponent
      },
      {
        path: 'relances',
        component:RelanceComponent
      },
      {
        path: 'institutions',
        component:ListeinstitutionComponent
      },
      {
        path: 'configrelance',
        component:ConfigrelanceComponent
      },
      {
        path: 'listthematique',
        component:ListtypeComponent
      },
      {
        path: 'listetapes',
        component:ListetapesComponent
      },
      {
        path: 'comment',
        component:RapCommentComponent
      },
      {
        path: 'listnature',
        component:ListenatureComponent
      },
      {
        path: 'dashboard',
        component:DashboardComponent
      },
      {
        path: 'profil',
        component:ProfilComponent
      },
      {
        path: 'listrequeteupdate',
        component:ListRequeteUpdateComponent
      },
      {
        path: 'listrequetestructures/:type_req',
        component:ListRequeteStructuresComponent
      },
      {
        path: 'listrequeteservice/:type_req',
        component:ListRequeteServicesComponent
      },
      {
        path: 'listrequetedivision/:type_req',
        component:ListRequeteDivisionComponent
      },
      {
        path: 'listrequeteusager/:type_req',
        component:ListRequeteUsagerComponent
      },
      {
        path: 'listrequeteparcours/:type_req',
        component:ParcoursRequeteComponent
      },
      {
        path: 'listregistre',
        component:ParcoursRegistreComponent
      },
      {
        path: 'statglob/:type_req/:col',
        component:StatspreocComponent
      },
      {
        path: 'listrequetepointreponse',
        component:PointReponseComponent
      },
      {
        path: 'listrequetepointpreoccupation',
        component:PointPreoccupationComponent
      },
      
      {
        path: 'listrdvs',
        component:ListRdvComponent
      },
      {
        path: 'liststatprestation',
        component:ListStatPrestationComponent
      },
      {
        path: 'liststatprestationbystructure',
        component:ListStatPrestationStructureComponent
      },
      {
        path: 'liststattheme/:type_req',
        component:ListStatThemeComponent
      },
      {
        path: 'liststatstructure/:type_req',
        component:ListStatStructureComponent
      },
      {
        path: 'listauxdigit',
        component:ListauxDigitComponent
      },
      {
        path: 'grahiqueevolution/:type_req',
        component:GraphiqueevolutionComponent
      },
      {
        path: 'grahiquetype/:type_req',
        component:GraphiquetypeComponent
      },
      {
        path: 'grahiquestructures/:type_req',
        component:GraphiquestructureComponent
      },
      {
        path: 'listrequeteajdoint/:type_req',
        component:ListRequeteAdjointComponent
      },
      
      {
        path: 'ratioplainteprestation',
        component:ListRatioPlaintePrestaionComponent
      },
      {
        path: 'ratiorequeteprestation',
        component:ListRatioRquetePrestaionComponent
      },
      {
        path: 'ratioplaintestructure',
        component:ListRatioPlainteStructureComponent
      },
      {
        path: 'ratiorequetestructure',
        component:ListRatioRequeteStructureComponent
      },
      {
        path: 'ratiodemandeinfosprestation',
        component:ListRatioDemandeInfosPrestationComponent
      },
      {
        path: 'ratiodemandeinfosstructure',
        component:ListRatioDemandeInfosStructureComponent
      },
      {path: '', redirectTo: 'dashboard', pathMatch: 'full'},
      {path: '**', redirectTo: 'dashboard', pathMatch: 'full'},
    ],
  },
  {path: '**', redirectTo: 'error/403', pathMatch: 'full'},
  //{path: '', redirectTo: 'espaceusager', pathMatch: 'full'},
  {
    path: 'error/403',
    component:Error403Component

  },
];

@NgModule({
  imports: [RouterModule.forRoot(routes,{
    onSameUrlNavigation:'reload'
  })],
  exports: [RouterModule]
})
export class AppRoutingModule { }
