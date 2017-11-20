
Vue.config.devtools = true;
Vue.config.debug = true;
Vue.http.headers.common['X-CSRF-TOKEN'] = document.querySelector('#token').getAttribute('value');

var com = Vue.component('bs',{
	template: '#bs-template',
	
	data: function(){
		return {
			value: '2015-01-01',
			list:[],
			remove:false,
			date1:null,
			date2:null,
			search:null,
			checked:null,
			startDatetime: moment().format("MMMM D, YYYY"),
		};
	},

	created: function(){
		this.fetchDataList();
	},

	methods:{
		fetchDataList: function(){
			var resource = this.$resource('balance_sheet/balance_sheet_data');	
			// resource.save(function(data){
			// 	this.list = data.results;
			// }.bind(this));

			resource.save().then(response => {
			// console.log(response);
				this.list = response.data.results;
			}, response => {
			// error callback
			});

		},
		collapse_list: function(){
			var resource = this.$resource('balance_sheet/bs_collapse');	
			// resource.save(function(data){
			// 	this.list = data.results;
			// }.bind(this));
			resource.save().then(response => {
			// console.log(response);
				this.list = response.data.results;
			}, response => {
			// error callback
			});


		},
		test:function(){
			alert("Wew");
		},
		filter1:function(){
			
			if(this.checked == true){
				var resource = this.$resource('balance_sheet/bs_collapse');	
				// resource.save({date1:this.date1,date2:this.date2,search:this.search},function(data){
				// 	this.list = data.results;
				// }.bind(this));
				resource.save({date1:this.date1,date2:this.date2,search:this.search}).then(response => {
					// console.log(response);
					this.list = response.data.results;
				});
			}else{
				var resource = this.$resource('balance_sheet/balance_sheet_data');
				resource.save({date1:this.date1,date2:this.date2,search:this.search}).then(response => {
					this.list = response.data.results;
				});
				
			}
		},
		colapse:function(){
			if(this.checked == true){
				this.collapse_list();

			}else{
				this.fetchDataList();
			}
		}

	},
    attached: function() {

       	var args = {
    		"autoApply":true,
            autoUpdateInput: true,
            singleDatePicker: true,
            locale: {
		        cancelLabel: 'Clear',
		        format: 'YYYY-MM-DD'
		    }
       	};
        this.$nextTick(function() {
            $('.datepick').daterangepicker(args)
	        
			$('.datepick').on('change', function(event) {
			  com.test();
			});
        });

    }



});


var vm = new Vue({
	el:'body',

	
});