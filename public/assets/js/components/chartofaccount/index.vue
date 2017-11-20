<div class="col-lg-12">
	<div class="ibox float-e-margins">
		<div class="ibox-title">
			<h5>Account Lists</h5>
		</div>
		<div class="ibox-content" id="ibox_form">
		<div class="pull-right">
        	<div class="form-group">
        	<a class="btn btn-md btn-primary" href="{{ action('ChartofaccountController@create_journal')}}"><span class="fa fa-plus"></span> Create General Journal</a>
        	<a class="btn btn-md btn-primary" href="{{ action('ChartofaccountController@create')}}"><span class="fa fa-plus"></span> Create Entry</a>
        	</div>
        </div>
        <div class="table-responsive">
			<table class="table table-bordered table-striped" id="tableSortable" style="text-transform:uppercase;">
	    		<thead>	
	    			<tr>
	    				<th>Account Title</th>
	    				<th>Account Type</th>
	                    <th>Balance</th>
	    				<th width = 80 class="text-center">Action</th>
	    			</tr>
	    		</thead>
	    		<tbody>
	    		<tr>
	    			<td></td>
	    		</tr>
	    		</tbody>
	    	</table>
	    	<div id="app-2">
			  <span v-bind:title="message">
			    Hover your mouse over me for a few seconds
			    to see my dynamically bound title!
			  </span>
			</div>
		</div>
		</div>


	</div>
</div>