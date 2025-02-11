import { BrowserModule } from '@angular/platform-browser';
import { NgModule } from '@angular/core';
import { CommonModule, DecimalPipe } from '@angular/common';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { HttpClientModule } from '@angular/common/http';
import { BrowserAnimationsModule } from '@angular/platform-browser/animations';
import { AppRoutingModule } from './app-routing.module';
import { AppComponent } from './app.component';
import { NgbModule } from '@ng-bootstrap/ng-bootstrap';
//partials
import { AsideComponent } from './views/partials/aside/aside.component';
import { HeaderComponent } from './views/partials/header/header.component';
import { FooterComponent } from './views/partials/footer/footer.component';
import { HeaderMobileComponent } from './views/partials/header-mobile/header-mobile.component';
import { ScrollToTopComponent } from './views/partials/scroll-to-top/scroll-to-top.component';
import { NotifPanelComponent } from './views/partials/notif-panel/notif-panel.component';
import { UserPanelComponent } from './views/partials/user-panel/user-panel.component';

//pages

import { ProfilComponent } from './views/pages/profil/profil.component';
import { BaseComponent } from './views/pages/base/base.component';
import { DashboardComponent } from './views/pages/dashboard/dashboard.component';
import { Error403Component } from './views/pages/error403/error403.component';
import { LoginComponent } from './views/pages/auth/login/login.component';
import { ForgotPasswordComponent } from './views/pages/auth/forgot-password/forgot-password.component';
import { ResetPasswordComponent } from './views/pages/auth/reset-password/reset-password.component';
import { CrudComponent } from './views/pages/crud/crud.component';

import { HighlightModule } from 'ngx-highlightjs';
import { NgSelectModule } from '@ng-select/ng-select';
// import { NgSelect2Module } from 'ng-select2';
import { NgxSpinnerModule } from 'ngx-spinner';
import {TranslateLoader, TranslateModule} from '@ngx-translate/core';
import {TranslateHttpLoader} from '@ngx-translate/http-loader';
import {HttpClient} from '@angular/common/http';
import { WebsocketService } from './core/_services/websocket.service';
import { ChartsModule } from 'ng2-charts';
import { RegisterComponent } from './views/pages/auth/register/register.component';
import { EventsComponent } from './views/pages/parameters/events/events.component';
import { ListacteursComponent } from './views/pages/parameters/listacteurs/listacteurs.component';
import { ListdaterdvComponent } from './views/pages/parameters/listdaterdv/listdaterdv.component';
import { ListetapesComponent } from './views/pages/parameters/listetapes/listetapes.component';
import { RapCommentComponent } from './views/pages/others/list-rap-comment/listrapcom.component';
import { ListefonctionComponent } from './views/pages/parameters/listefonction/listefonction.component';
import { ListeinstitutionComponent } from './views/pages/parameters/listeinstitution/listeinstitution.component';
import { ConfigrelanceComponent } from './views/pages/parameters/configrelance/configrelance.component';
import { ListenatureComponent } from './views/pages/parameters/listenature/listenature.component';
import { ListeprofilsComponent } from './views/pages/parameters/listeprofils/listeprofils.component';
import { ListerdvcrenauxComponent } from './views/pages/parameters/listerdvcrenaux/listerdvcrenaux.component';
import { ListerdvcjourComponent } from './views/pages/parameters/listerdvcjour/listerdvcjour.component';
import { ListerdvparametreComponent } from './views/pages/parameters/listerdvparametre/listerdvparametre.component';
import { ListeserviceComponent } from './views/pages/parameters/listeservice/listeservice.component';
import { AttributcomComponent } from './views/pages/parameters/attributcom/attributcom.component';
import { ListestructuresComponent } from './views/pages/parameters/listestructures/listestructures.component';
import { ListtypeComponent } from './views/pages/parameters/listtype/listtype.component';
import { ListusagerComponent } from './views/pages/parameters/listusager/listusager.component';
import { UsersComponent } from './views/pages/users/users.component';
import { EspaceusagerComponent } from './views/pages/others/espaceusager/espaceusager.component';
import { EspacepointfocalcomComponent } from './views/pages/others/espacepointfocalcom/espacepointfocalcom.component';
import { GraphiqueevolutionComponent } from './views/pages/others/graphiqueevolution/graphiqueevolution.component';
import { GraphiquestructureComponent } from './views/pages/others/graphiquestructure/graphiquestructure.component';
import { GraphiquetypeComponent } from './views/pages/others/graphiquetype/graphiquetype.component';
import { ListplainteComponent } from './views/pages/others/listplainte/listplainte.component';
import { ListRatioPlaintePrestaionComponent } from './views/pages/others/list-ratio-plainte-prestaion/list-ratio-plainte-prestaion.component';
import { ListRatioPlainteStructureComponent } from './views/pages/others/list-ratio-plainte-structure/list-ratio-plainte-structure.component';
import { ListRatioRquetePrestaionComponent } from './views/pages/others/list-ratio-rquete-prestaion/list-ratio-rquete-prestaion.component';
import { ListRatioRequeteStructureComponent } from './views/pages/others/list-ratio-requete-structure/list-ratio-requete-structure.component';
import { ListRdvJourComponent } from './views/pages/others/list-rdv-jour/list-rdv-jour.component';
import { ListRdvComponent } from './views/pages/others/list-rdv/list-rdv.component';
import { ListRdvUsagerComponent } from './views/pages/others/list-rdv-usager/list-rdv-usager.component';
import { ListRequeteDivisionComponent } from './views/pages/others/list-requete-division/list-requete-division.component';
import { ListRequeteServicesComponent } from './views/pages/others/list-requete-services/list-requete-services.component';
import { ListRequeteStructuresComponent } from './views/pages/others/list-requete-structures/list-requete-structures.component';
import { ListRequeteUpdateComponent } from './views/pages/others/list-requete-update/list-requete-update.component';
import { ListRequeteUsagerComponent } from "./views/pages/others/list-requete-usager/list-requete-usager.component";
import { ListStatPrestationComponent } from './views/pages/others/list-stat-prestation/list-stat-prestation.component';
import { ListStatReponseComponent } from './views/pages/others/list-stat-reponse/list-stat-reponse.component';
import { ListStatStructureComponent } from './views/pages/others/list-stat-structure/list-stat-structure.component';
import { ListauxDigitComponent } from './views/pages/others/list-taux-digita/list-taux-digita.component';
import { ListStatThemeComponent } from './views/pages/others/list-stat-theme/list-stat-theme.component';
import { NoterComponent } from './views/pages/others/noter/noter.component';
import { ParcoursRequeteComponent } from './views/pages/others/parcours-requete/parcours-requete.component';
import { ParcoursRegistreComponent } from './views/pages/others/parcours-registre/parcours-registre.component';
import { StatspreocComponent } from './views/pages/others/stats-preoc/stats-preoc.component';
import { PointReponseComponent } from './views/pages/others/point-reponse/point-reponse.component';
import { PointPreoccupationComponent } from './views/pages/others/point-preoccupation/point-preoccupation.component';
import { RequetesComponent } from './views/pages/others/requetes/requetes.component';
import { LoginUsagerComponent } from './views/pages/auth/login-usager/login-usager.component';
import { PaginationComponent } from './views/components/pagination/pagination.component';
import { JwtModule } from "@auth0/angular-jwt";
import { SafeUrlPipe } from './safe-url.pipe';
import { ListRequeteAdjointComponent } from './views/pages/others/list-requete-adjoint/list-requete-adjoint.component';
import { BaseUsagerComponent } from './views/pages/base-usager/base-usager.component';
import { ListRatioDemandeInfosStructureComponent } from './views/pages/others/list-ratio-demande-infos-structure/list-ratio-demande-infos-structure.component';
import { ListRatioDemandeInfosPrestationComponent } from './views/pages/others/list-ratio-demande-infos-prestation/list-ratio-demande-infos-prestation.component';
import { RelanceComponent } from './views/pages/others/relance/relance.component';
import { UsersMainComponent } from './views/pages/users-main/users-main.component';
import { ListSynthesStructureComponent } from './views/pages/others/list-synthes-structure/list-synthes-structure.component';
import { ListSuggestionComponent } from './views/pages/others/list-suggestion/list-suggestion.component';
import { GuideComponent } from './views/pages/others/guide/guide.component';
import { ListDenonciationComponent } from './views/pages/others/list-denonciation/list-denonciation.component';
import { ListStatPrestationStructureComponent } from './views/pages/list-stat-prestation-structure/list-stat-prestation-structure.component';
import { ListserviceatraiterComponent } from './views/pages/parameters/listserviceatraiter/listserviceatraiter.component';
import { AllServicesComponent } from './views/pages/all-services/all-services.component';
import { ManageRequeteUsagerComponent } from './views/pages/others/manage-requete-usager/manage-requete-usager.component';
import { ComplementInformationComponent } from './views/pages/others/complement-information/complement-information.component';
import { LoginV2Component } from './views/pages/auth/login-v2/login-v2.component';
import { LoginCheckComponent } from './views/pages/auth/login-check/login-check.component';
import { SettingsComponent } from './views/pages/settings/settings.component';
import { EservicesComponent } from './views/pages/eservices/eservices.component';
import { CcspComponent } from './views/pages/ccsp/ccsp.component';
import { TypeStructureComponent } from './views/pages/type-structure/type-structure.component';
import { NatureContractComponent } from './views/pages/nature-contract/nature-contract.component';
import { AvancedStatisticsComponent } from './views/pages/avanced-statistics/avanced-statistics.component';
// import { BnNgIdleService } from 'bn-ng-idle'; // import bn-ng-idle service 

@NgModule({
  declarations: [
    SafeUrlPipe,
    AppComponent,
    AsideComponent,
    LoginComponent,
    ForgotPasswordComponent,
    BaseComponent,
    DashboardComponent,
    Error403Component,                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            
    HeaderComponent,
    FooterComponent,
    HeaderMobileComponent,                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                 
    ScrollToTopComponent,
    NotifPanelComponent,
    UserPanelComponent,
    ProfilComponent,
    CrudComponent,
    ResetPasswordComponent,
    RegisterComponent,
    EventsComponent,
    ListacteursComponent,
    ListdaterdvComponent,
    ListetapesComponent,
    RapCommentComponent,
    ListefonctionComponent,
    ConfigrelanceComponent,
    ListeinstitutionComponent,
    ListenatureComponent,
    ListeprofilsComponent,
    ListerdvcrenauxComponent,
    ListerdvcjourComponent,
    ListerdvparametreComponent,
    ListeserviceComponent,
    AttributcomComponent,
    ListestructuresComponent,
    ListtypeComponent,
    ListusagerComponent,
    UsersComponent,
    EspaceusagerComponent,
    EspacepointfocalcomComponent,
    GraphiqueevolutionComponent,
    GraphiquestructureComponent,
    GraphiquetypeComponent,
    ListplainteComponent,
    ListRatioPlaintePrestaionComponent,
    ListRatioPlainteStructureComponent,
    ListRatioRquetePrestaionComponent,
    ListRatioRequeteStructureComponent,
    ListRdvJourComponent,
    ListRdvComponent,
    ListRdvUsagerComponent,
    ListRequeteDivisionComponent,
    ListRequeteServicesComponent,
    ListRequeteStructuresComponent,
    ListRequeteUpdateComponent,
    ListRequeteUsagerComponent,
    ListStatPrestationComponent,
    ListStatReponseComponent,
    ListStatStructureComponent,
    ListauxDigitComponent,
    ListStatThemeComponent,
    NoterComponent,
    ParcoursRequeteComponent,
    ParcoursRegistreComponent,
    StatspreocComponent,
    PointReponseComponent,
    RequetesComponent,
    LoginUsagerComponent,
    PaginationComponent,
    ListRequeteAdjointComponent,
    BaseUsagerComponent,
    ListRatioDemandeInfosStructureComponent,
    ListRatioDemandeInfosPrestationComponent,
    RelanceComponent,
    UsersMainComponent,
    ListSynthesStructureComponent,
    ListSuggestionComponent,
    ListDenonciationComponent,
    ListStatPrestationStructureComponent,
    ListserviceatraiterComponent,
    AllServicesComponent,
    PointPreoccupationComponent,
    ManageRequeteUsagerComponent,
    ComplementInformationComponent,
    LoginV2Component,
    LoginCheckComponent,
    GuideComponent,
    SettingsComponent,
    EservicesComponent,
    CcspComponent,
    TypeStructureComponent,
    NatureContractComponent,
    AvancedStatisticsComponent
  ],
  imports: [
    BrowserModule,
    BrowserAnimationsModule,
    NgxSpinnerModule,
    AppRoutingModule,
    CommonModule,
    HttpClientModule,
    FormsModule,
    ReactiveFormsModule,
    NgbModule,
    NgSelectModule,
    // NgSelect2Module,
    HighlightModule,
    ChartsModule,
    TranslateModule.forRoot({
      loader: {
          provide: TranslateLoader,
          useFactory: HttpLoaderFactory,
          deps: [HttpClient]
      }
    }),
    JwtModule.forRoot({
      config: {
        tokenGetter: (request) => {
          return localStorage.getItem("access_token");
        },
      },
    })
     
    ],
    providers: [DecimalPipe,WebsocketService],
  bootstrap: [AppComponent]
})
export class AppModule { }

export function HttpLoaderFactory(http: HttpClient) {
  return new TranslateHttpLoader(http);
}

