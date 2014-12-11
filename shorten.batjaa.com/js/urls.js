/**
 * @jsx React.DOM
 */
/*jshint quotmark:false */
/*jshint white:false */
/*jshint trailing:false */
/*jshint newcap:false */
/*global React, Backbone, JQuery */
var app = app || {};

(function () {
	'use strict';

	// Urls Collection
	// ---------------
	var Urls = Backbone.Collection.extend({
		// Reference to this collection's model.
		model: app.Url,
		parse: function(response) {
			console.log(response.data);
			return response.data;
		},
		url: 'http://www.shorten.batjaa.com/api/v1/urls'
	});

	// Create our global collection of **Urls**.
	app.urls = new Urls();
})();
