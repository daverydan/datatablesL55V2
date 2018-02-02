<template>
  <div class="panel panel-default">
      <div class="panel-heading">{{ response.table }}</div>

      <div class="panel-body">
      	<!-- {{ response.records }} -->
        <div class="table-responsive">
        	<table class="table table-striped">
        		<thead>
        			<tr>
        				<th v-for="column in response.displayable">
        					{{ column }}
        				</th>
        				<th>&nbsp;</th>
        			</tr>
        		</thead>
        	
        		<tbody>
        			<tr v-for="record in response.records">
<!-- column = property : columnValue = value (property: value / column: value) -->
        				<td v-for="columnValue, column in record">{{ columnValue }}</td>
        				<td>Edit</td>
        			</tr>
        		</tbody>
        	</table>
        </div>
      </div>
  </div>
</template>

<script>
	import axios from 'axios';

    export default {
    	props: ['endpoint'],

    	data () {
    		return {
    			response: {
    				displayable: [],
    				records: []
    			}
    		}
    	},
      
      mounted () {
      	// console.log(this.endpoint);
      	this.getRecords()
      },

      methods: {
      	getRecords () {
      		// console.log(this.endpoint);
					return axios.get(`${this.endpoint}`).then((response) => {
						// console.log(response.data.data);
						this.response = response.data.data
					})
      	}
      }
    }
</script>
