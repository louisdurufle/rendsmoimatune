<template>
<div v-if="$loadingRouteData">
	<loading></loading>
</div>
<div v-if="!$loadingRouteData">
	<div class="row">
		<div class="small-12 columns">
			<h2><a v-link="{ name: 'account', params: { accountId: account.uid } }"><i class="fa fa-bank fa-lg fa-fw"></i>{{ account.name }}</a></h2>
		</div>
	</div>
	<router-view :account="account"></router-view>
</div>
</template>

<script>
export default {
	data () {
		return {'account': {}}
	},
	route: {
		data () {
			var resource = this.$resource('account/{id}')

			return resource.get({id: this.$route.params.accountId}).then(function (response) {
				this.account = response.data
			}).catch(function () {
				// TODO handle error
			})
		}
	}
}
</script>

<style>
</style>
