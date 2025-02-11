import { Component } from '@angular/core';
import {TranslateService} from '@ngx-translate/core';
// import { BnNgIdleService } from 'bn-ng-idle'; // import it to your component ,private bnIdle: BnNgIdleService
import { Router } from '@angular/router'; 

declare var $: any;
import { ConnectionService } from 'ng-connection-service';  

@Component({
  selector: 'app-root',
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.css']
})
export class AppComponent {
  title = 'mataccueil';
  isConnected = true; 
  message;
  constructor(private connectionService: ConnectionService,private router:Router) {
    this.connectionService.monitor().subscribe(isConnected => {
      this.isConnected = isConnected;
      if (!this.isConnected) {
        /*
        $.toast({
          heading: 'Attention',
          text: "Verifier votre connexion internet. Si vous déjà etes connectez alors verifier que vous avez une bonne connexion",
          position: 'top-right',
          loaderBg:'#f70909',
          icon: 'error',
          hideAfter: 30000, 
          stack: 6
        });
        */
      }
     
    })
  }
  
  // ngOnInit(): void {
    
  //   this.bnIdle.startWatching(300).subscribe((isTimedOut: boolean) => {
  //     // alert('Votre session a expirée. Prière vous reconnecter.');
  //     localStorage.removeItem("mataccueilToken")
  //     localStorage.removeItem("mataccueilUserData")
  //     this.router.navigateByUrl('/login-v2')
  //     this.router.navigateByUrl('/login')
  //     this.bnIdle.stopTimer();
  //   });
  // }
}
