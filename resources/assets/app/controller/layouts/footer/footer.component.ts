import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup } from '@angular/forms';
import { RouterModule, Router }   from '@angular/router';
import { ApiMethodService } from '../../../model/api-method.service';
import 'rxjs/add/operator/map';
import 'rxjs/add/operator/catch';


@Component({
	selector: 'app-footer',
	templateUrl: '../../../view/layouts/footer/footer.component.html',
	styleUrls: ['../../../assets/css/layouts/footer/footer.component.css'
	]
})
export class FooterComponent implements OnInit {

	constructor(private router:Router, public apiService:ApiMethodService) { }

	ngOnInit() {
	}

	userSignIn(value:any):void{

		var ref = this;
		this.apiService.userLoginApi(value,function(res){
			console.log("this is api response"+ JSON.stringify(res));
			if(res.data.token){
				window.location.reload();
			}
							
		});
	}
	

	userSignUp(value:any):void{

		var refreg = this;
		this.apiService.userRegistrationApi(value,function(res){
			console.log("this is api response"+ JSON.stringify(res));
			window.location.reload();
		});
	}


}

