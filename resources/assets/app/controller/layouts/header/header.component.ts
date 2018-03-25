import { Component, OnInit, Output, EventEmitter} from '@angular/core';
import { RouterModule, Router }   from '@angular/router';
import { FormBuilder, FormGroup } from '@angular/forms';
import { ApiMethodService } from '../../../model/api-method.service';
import 'rxjs/add/operator/map';
import 'rxjs/add/operator/catch';

@Component({
  selector: 'app-header',
  templateUrl: '../../../view/layouts/header/header.component.html',
  styleUrls: ['../../../assets/css/layouts/header/header.component.css'
  ]
})
export class HeaderComponent implements OnInit {
  getToken:any;
	  @Output() notify: EventEmitter<any> = new EventEmitter<any>();


  constructor(private router: Router, public apiService:ApiMethodService) { }

  ngOnInit() {
    this.getToken = this.apiService.getLoginToken();
    console.log("this is token"+this.getToken);
  }

  goToBlog(){
  	this.router.navigate(['/blog']);
  }

  userLogout(){
    this.getToken="";
    var ref = this;
    this.apiService.userLogoutApi(function(res){
      console.log("this is api response"+ JSON.stringify(res));
      window.location.reload();
    });
  }
}
