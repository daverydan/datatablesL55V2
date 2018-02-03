<template>
  <div class="panel panel-default">
      <div class="panel-heading">
      	{{ response.table | capitalize }}
      	<a href="#" class="pull-right" v-if="response.allow.creation" @click.prevent="creating.active = !creating.active">
      		{{ creating.active ? 'Cancel' : '+ New Record' }}
      	</a>
      </div>

      <div class="panel-body">
      	<div class="well" v-if="creating.active">
      		<form action="#" class="form-horizontal" @click.prevent="store">
      			<div class="form-group" v-for="column in response.updatable">
      				<label class="col-md-3 control-label" :for="column">{{ column }}</label>
      				<div class="col-md-6">
      					<input type="text" :id="column" class="form-control" v-model="creating.form[column]">
      				</div>
      			</div>

      			<div class="form-group">
      				<div class="col-md-6 col-md-offset-3">
      					<button type="submit" class="btn btn-default">Create</button>
      				</div>
      			</div>
      		</form>
      	</div>
				<form action="#" @submit.prevent="getRecords">
					<label for="saerch">Search</label>
					<div class="row row-fluid">
						<div class="form-group col-md-3">
							<select class="form-control" v-model="search.column">
								<option :value="column" v-for="column in response.displayable">{{ column }}</option>
							</select>
						</div>
						<div class="form-group col-md-3">
							<select class="form-control" v-model="search.operator">
								<option value="equals">=</option>
								<option value="greater_than">&gt;</option>
								<option value="less_than">&lt;</option>
								<option value="greater_than_or_equal_to">&ge;</option>
								<option value="less_than_or_equal_to">&le;</option>
								<option value="contains">contains</option>
								<option value="starts_with">starts with</option>
								<option value="ends_with">ends with</option>
							</select>
						</div>
						<div class="form-group col-md-6">
							<div class="input-group">
								<input type="text" class="form-control" id="search" v-model="search.value">
								<span class="input-group-btn">
									<button class="btn btn-default" type="submit">Search</button>
								</span>
							</div>
						</div>
					</div>
				</form>

				<div class="row">
					<div class="form-group col-md-10">
						<label for="filter">Quick search current results</label>
						<input type="text" id="filter" class="form-control" v-model="quickSearchQuery">
					</div>

					<div class="form-group col-md-2">
						<label for="limit">Display records</label>
						<select id="limit" class="form-control" v-model="limit" @change="getRecords">
							<option value="50">50</option>
							<option value="100">100</option>
							<option value="1000">1000</option>
							<option value="">All</option>
						</select>
					</div>
				</div>

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
        			<!-- {{ editing.form }} -->
        			<!-- <tr v-for="record in response.records"> -->
        			<tr v-for="record in filteredRecords">
<!-- column = property : columnValue = value (property: value / column: value) -->
        				<td v-for="columnValue, column in record">
        					<template v-if="editing.id === record.id && isUpdatable(column)">
														        						<!-- bind class if editing.errors[column] -->
        						<div class="form-group" :class="{ 'has-error': editing.errors[column] }">
        							<input type="text" class="form-control" value="columnValue" v-model="editing.form[column]">
        							<div class="help-block" v-if="editing.errors[column]">
        								<strong>{{ editing.errors[column][0] }}</strong>
        							</div>
        						</div>
        					</template>
        					<template v-else>
        						{{ columnValue }}
        					</template>
        				</td>
        				<td>
        					<a href="#" @click.prevent="edit(record)" v-if="editing.id !== record.id">Edit</a>
        					<template v-if="editing.id === record.id">
        						<a href="#" @click.prevent="update">Save</a> <br>
        						<a href="#" @click.prevent="editing.id = null">Cancel</a>
        					</template>
        				</td>
        			</tr>
        		</tbody>
        	</table>
        </div>
      </div>
  </div>
</template>

<script>
	import queryString from 'query-string';

    export default {
    	props: ['endpoint'],

    	data () {
    		return {
    			response: {
    				displayable: [],
    				records: [],
    				allow: {}
    			},
    			sort: {
    				key: 'id', // column name default
    				order: 'asc'
    			},
    			limit: 50,
    			quickSearchQuery: '',
    			editing: {
    				id: null,
    				form: {},
    				errors: []
    			},
    			search: {
    				value: '',
    				operator: 'equals',
    				column: 'id'
    			},
    			creating: {
    				active: false,
    				form: {},
    				errors: []
    			}
    		}
    	},
      
			computed: {
				filteredRecords () {
					let data = this.response.records

					// foreach row 
					data = data.filter((row) => {
						// Object.keys = id, name, email, created_at
						// console.log(Object.keys(row))
						return Object.keys(row).some((key) => {
							// console.log(String(row[key]));
							// position greater than 0
							return String(row[key]).toLowerCase().indexOf(this.quickSearchQuery.toLowerCase()) > -1
						})
					})

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
      		// console.log(this.getQueryParameters());
					return axios.get(`${this.endpoint}?${this.getQueryParameters()}`).then((response) => {
						// console.log(response.data.data);
						this.response = response.data.data
					})
      	},
      	getQueryParameters () {
					return queryString.stringify({
						limit: this.limit,
						// https://babeljs.io/docs/plugins/transform-object-rest-spread/
						...this.search
					})
      	},
      	sortBy (column) {
      		// console.log(column)
      		this.sort.key = column
      		this.sort.order = this.sort.order === 'asc' ? 'desc' : 'asc'
      		// console.log(this.sort)
      	},
      	edit (record) {
      		this.editing.errors = [] // clear out errors
      		this.editing.id = record.id
      		// pick out editable columns w/ Lodash
      		// console.log(_.pick(record, this.response.updatable));
      		this.editing.form = _.pick(record, this.response.updatable)
      	},
      	isUpdatable (column) {
      		// only show on updatable fields
      		return this.response.updatable.includes(column)
      	},
      	update () {
      		axios.patch(`${this.endpoint}/${this.editing.id}`, this.editing.form).then(() => {
						// after update, fetch records
						this.getRecords().then(() => {
							this.editing.id = null
							this.editing.form = {}
						})
      		}).catch((error) => {
						this.editing.errors = error.response.data.errors
      		})
      	},
      	store () {
      		console.log(this.creating.form)
      	}
      },

      filters: {
        capitalize: function (value) {
          if (!value) return ''
          value = value.toString()
          return value.charAt(0).toUpperCase() + value.slice(1)
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