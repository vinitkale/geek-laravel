import { Component, OnInit } from '@angular/core';
import { RouterModule, Router }   from '@angular/router';
import { ApiMethodService } from '../../model/api-method.service';
import 'rxjs/add/operator/map';
import 'rxjs/add/operator/catch';

@Component({
  selector: 'app-blog',
  templateUrl: '../../view/blog/blog.component.html',
  styleUrls: ['../../assets/css/blog/blog.component.css']
})
export class BlogComponent implements OnInit {

	constructor(private router:Router, public apiService:ApiMethodService) { }

  ngOnInit() {
  	this.blogDeafault();
  }

  blogDeafault(){
		var ref = this;
		this.apiService.blogApi(function(res){
			console.log("this is blog api response"+ JSON.stringify(res));			
		});
	}

}
