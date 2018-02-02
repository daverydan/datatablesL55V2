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
        					<span class="sortable" @click="sortBy(column)">{{ column }}</span>
        					<div 
        						class="arrow"
        						v-if="sort.key ===column"
        						:class="{ 'arrow--asc': sort.order === 'asc', 'arrow--desc': sort.order === 'desc' }"
      						></div>
        				</th>
        				<th>&nbsp;</th>
        			</tr>
        		</thead>
        	
        		<tbody>
        			<!-- <tr v-for="record in response.records"> -->
        			<tr v-for="record in filteredRecords">
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
    			},
    			sort: {
    				key: 'id', // column name default
    				order: 'asc'
    			}
    		}
    	},
      
			computed: {
				filteredRecords () {
					let data = this.response.records

					if (this.sort.key) {
						data = _.orderBy(data, (i) => {
							let value = i[this.sort.key] // order by column clicked
							// console.log(value) // outputs all the ids
							// parse the integers (ids)
							if (!isNaN(parseFloat(value))) {
								return parseFloat(value)
							}
							// cast value to a string
							return String(i[this.sort.key]).toLowerCase()
						}, this.sort.order) // 3rd argument of _.orderBy
					}

					return data
				}
			},
      
      methods: {
      	getRecords () {
      		// console.log(this.endpoint);
					return axios.get(`${this.endpoint}`).then((response) => {
						// console.log(response.data.data);
						this.response = response.data.data
					})
      	},
      	sortBy (column) {
      		// console.log(column)
      		this.sort.key = column
      		this.sort.order = this.sort.order === 'asc' ? 'desc' : 'asc'
      		// console.log(this.sort)
      	}
      },

      mounted () {
      	// console.log(this.endpoint);
      	this.getRecords()
      }
    }
</script>

<style lang="scss">
	.sortable {
		cursor: pointer;
	}

	.arrow {
		display: inline-block;
		vertical-align: middle;
		width: 0;
		height: 0;
		margin-left: 5px;
		opacity: .6;

		&--asc {
			border-left: 4px solid transparent;
			border-right: 4px solid transparent;
			border-bottom: 4px solid #222;
		}

		&--desc {
			border-left: 4px solid transparent;
			border-right: 4px solid transparent;
			border-top: 4px solid #222;
		}
	}
</style>