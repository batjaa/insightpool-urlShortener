/*global Backbone */
var app = app || {};

(function () {
	'use strict';

	// Url Model
	// ----------

	app.Url = Backbone.Model.extend({
		urlRoot: 'http://www.shorten.batjaa.com/api/v1/urls',
		// Default attributes for the todo
		// and ensure that each todo created has `title` and `completed` keys.
		defaults: {
			id: null,
			key: '',
			url: '',
			created_date: null,
			showDetails: false
		},

		// Toggle the `showDetails` state of this todo item.
		toggleShowDetails: function () {
			this.set('showDetails', !this.get('showDetails'));
		}

	});
})();
