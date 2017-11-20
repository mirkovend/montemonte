Vue.config.devtools = true;
Vue.config.debug = true;
Vue.http.headers.common['X-CSRF-TOKEN'] = document.querySelector('#token').getAttribute('value');
Vue.component('profit',{
	template: '#profit',

	data: function(){
		return {
			list:[],
			remove:false,
			loading:false,
			pagination:{},
			checked:null,
			query:"",
		};
	},

	created: function(){
		this.fetchDataList();
	},

	methods:{
		fetchDataList: function(page_url){
			// this.loading = true;
			// page_url = page_url || 'chartofaccount_data';
  			var resource = this.$resource('profitloss/data');	
	
			resource.get().then(response => {
			
			   	this.list = response.data.results;
            	// this.pagination = response.data.results;
            	// this.loading = false;
			}, response => {
			// error callback
			});
		},
		collapseList: function(){
			
			// page_url = page_url || 'chartofaccount_data';
  			var resource = this.$resource('profitloss/profitloss_collapse');	
	
			resource.get().then(response => {
				
			   	this.list = response.data.results;
            	// this.pagination = response.data.results;
            
			}, response => {
			// error callback
			});
		},
		collapse:function(){
			if(this.checked == true){
				this.collapseList();

			}else{
				this.fetchDataList();
			}
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
	},

});

new Vue({
	el:'body',
});