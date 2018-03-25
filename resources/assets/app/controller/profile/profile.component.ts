import { Component, OnInit } from '@angular/core';
import { ApiMethodService } from '../../model/api-method.service';
import { RouterModule, Router }   from '@angular/router';

import 'rxjs/add/operator/map';
import 'rxjs/add/operator/catch';

@Component({
	selector: 'app-profile',
	templateUrl: '../../view/profile/profile.component.html',
	styleUrls: ['../../assets/css/profile/profile.component.css']
})
export class ProfileComponent implements OnInit {
	getToken:any;

	constructor(private router: Router,public apiService:ApiMethodService) { }

	ngOnInit() {
		this.getToken = this.apiService.getLoginToken();
		if(!(this.getToken)){
			this.router.navigate(['/index']);
		}
	}

}
