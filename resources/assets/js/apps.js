Vue.config.devtools = true;
Vue.config.debug = true;
Vue.http.headers.common['X-CSRF-TOKEN'] = document.querySelector('#token').getAttribute('value');
Vue.component('tasks',{
	template: '#tasks-template',

	data: function(){
		return {
			list:[],
			remove:false,
			loading:false,
			pagination:{},
			query:"",
		};
	},

	created: function(){
		this.fetchDataList();
	},

	methods:{
		fetchDataList: function(page_url){
			this.loading = true;
			page_url = page_url || 'chartofaccount_data';
  			var resource = this.$resource(page_url);	
	
			resource.save({query:this.query}).then(response => {
				// console.log(response.data);
			   	this.list = response.data.results.data;
            	this.pagination = response.data.results;
            	this.loading = false;
			}, response => {
			// error callback
			});
		},
        makePagination: function(data){
            let pagination = {
                current_page: data.current_page,
                last_page: data.last_page,
                next_page_url: data.next_page_url,
                prev_page_url: data.prev_page_url
            }
           
            this.$set('pagination', pagination);
        },

		deleteCoa: function(data){
			
			// var resource = this.$resource('chartofaccount_data/api/delete/:id');	
			var ConfirmBox = confirm("Are you sure you want to delete "+data.coa_title);

			if(ConfirmBox){ 
			
				// resource.delete({id:data.id});
				this.$http.delete('chartofaccount_data/api/delete/' + data.id);
				
				self = this;

				this.remove = true;
				setTimeout(function (){
					self.remove = false;
				}, 5000);
				this.fetchDataList();	
			
			}
		},
	},


});

new Vue({
	el:'body',
});