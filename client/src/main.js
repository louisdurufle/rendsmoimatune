import 'src/css/main.scss'
import { foundation } from 'foundation-sites/js/foundation.core.js'
$.fn.foundation = foundation
import 'foundation-sites/js/foundation.util.keyboard.js'
import 'foundation-sites/js/foundation.util.box.js'
import 'foundation-sites/js/foundation.util.triggers.js'
import 'foundation-sites/js/foundation.util.mediaQuery.js'
import 'foundation-sites/js/foundation.util.motion.js'
import 'foundation-sites/js/foundation.reveal.js'
import 'foundation-datepicker/js/foundation-datepicker.js'
import 'foundation-datepicker/scss/foundation-datepicker.scss'
import Vue from 'vue'
import VueRouter from 'vue-router'
import VueResource from 'vue-resource'
import App from './app'
import Landing from './landing'
import Loading from './loading'
import Account from './account'
import AccountDetail from './account-detail'
import Expenditures from './expenditures'
import EditExpenditure from './edit-expenditure'
import ListExpenditures from './list-expenditures'
import EditRepayment from './edit-repayment'
import ListRepayment from './list-repayments'
import Repayments from './repayments'

Vue.use(VueRouter)
Vue.use(VueResource)

Vue.component('loading', Loading)
Vue.component('expenditures', Expenditures)
Vue.component('repayments', Repayments)

Vue.filter('currency', function (amount) {
	return Math.round(amount) * 1.0 / 100 + ' €'
})

Vue.filter('amount', {
	read: function (amount) {
		return Math.round(amount) * 1.0 / 100
	},
	write: function (amount) {
		amount = amount.replace(/,/g, '.')
		return isNaN(amount) ? 0 : Math.round(parseFloat(amount) * 100)
	}
})

Vue.directive('date-picker', {
	twoWay: true,
	params: ['format', 'language'],
	bind: function () {
		var self = this
		$(this.el).fdatepicker({
			initialDate: this.value,
			format: this.params.format,
			language: this.params.language
		}).on('changeDate', function (ev) {
			self.set(ev.date)
		})
	},
	update: function (newValue) {
		$(this.el).fdatepicker('update', newValue)
	}
})

Vue.directive('reveal-open', {
	update: function (value) {
		if (typeof oldValue !== 'undefined') {
			$(this.el).unbind('click', this.handler)
		}

		this.handler = function () {
			var reveal = $('#' + value)
			if (reveal.length === 0) {
				console.error('unknown reveal element: ' + value)
				return
			}
			reveal = reveal[0]
			$(reveal).foundation('open')
		}

		$(this.el).click(this.handler)
	}
})

Vue.directive('reveal-close', {
	update: function (value, oldValue) {
		if (typeof oldValue !== 'undefined') {
			$(this.el).unbind('click', this.handler)
		}
		var self = this
		this.handler = function () {
			var reveal
			if (typeof value !== 'undefined') {
				reveal = $('#' + value)
			} else {
				reveal = $(self.el).parents('[data-reveal]')
			}
			if (reveal.length === 0) {
				console.error('unknown reveal element: ' + value)
				return
			}
			reveal = reveal[0]
			$(reveal).foundation('close')
		}

		$(this.el).click(this.handler)
	}
})

Vue.directive('reveal-data', {
	bind: function () {
		this.reveal = new Foundation.Reveal($(this.el))
	}
})

Vue.http.options.root = '/api'

const router = new VueRouter({
	history: false,
	saveScrollPosition: true
})

router.map({
	'/': {
		component: Landing
	},
	'/account/:accountId': {
		name: 'account',
		component: Account,
		subRoutes: {
			'/': {
				component: AccountDetail
			},
			'/create-expenditure': {
				name: 'create-expenditure',
				component: EditExpenditure
			},
			'/edit-expenditure/:expenditureId': {
				name: 'edit-expenditure',
				component: EditExpenditure
			},
			'/expenditures': {
				name: 'expenditures',
				component: ListExpenditures
			},
			'/create-repayment': {
				name: 'create-repayment',
				component: EditRepayment
			},
			'/create-repayment/:amount/from/:payer/to/:beneficiary': {
				name: 'add-repayment',
				component: EditRepayment
			},
			'/edit-repayment/:repaymentId': {
				name: 'edit-repayment',
				component: EditRepayment
			},
			'/repayments': {
				name: 'repayments',
				component: ListRepayment
			}
		}
	}
})

router.start(App, 'body')
