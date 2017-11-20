Vue.config.devtools = true;
Vue.config.debug = true;
Vue.http.headers.common['X-CSRF-TOKEN'] = document.querySelector('#token').getAttribute('value');
Vue.component('payment',{
	template: '#payment',

	data: function(){
		return {
			addRows:[],
		};
	},

	created: function(){

	},

	methods:{
		addRow: function(){
            this.addRows.push({
                status:null,
            });
        },

        saveRow:function(){
        	var resource = this.$resource('/montemonte/payments/test');	
				// resource.save({date1:this.date1,date2:this.date2,search:this.search},function(data){
				// 	this.list = data.results;
				// }.bind(this));
				resource.save({data:this.addRows}).then(response => {
					console.log(response);
					// this.list = response.data.results;
				});
        }
	},

});

new Vue({
	el:'body',
});
