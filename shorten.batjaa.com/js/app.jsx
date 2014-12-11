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

	app.ALL_TODOS = 'all';
	var Url = app.Url;
	var UrlItem = app.UrlItem;

	var ENTER_KEY = 13;

	var BackboneMixin = {
		componentDidMount: function () {
			// Whenever there may be a change in the Backbone data, trigger a
			// reconcile.
			this.getBackboneCollections().forEach(function (collection) {
				// explicitly bind `null` to `forceUpdate`, as it demands a callback and
				// React validates that it's a function. `collection` events passes
				// additional arguments that are not functions
				collection.on('add remove change', this.forceUpdate.bind(this, null));
			}, this);
		},

		componentWillUnmount: function () {
			// Ensure that we clean up any dangling references when the component is
			// destroyed.
			this.getBackboneCollections().forEach(function (collection) {
				collection.off(null, null, this);
			}, this);
		}
	};

	var TodoApp = React.createClass({
		mixins: [BackboneMixin],
		getBackboneCollections: function () {
			return [this.props.urls];
		},

		getInitialState: function () {
			return {editing: null};
		},

		componentDidMount: function () {
			var Router = Backbone.Router.extend({
				routes: {
					'': 'all',
				},
				all: this.setState.bind(this, {nowShowing: app.ALL_TODOS}),
			});

			new Router();
			Backbone.history.start();

			this.props.urls.fetch();
		},

		handleNewUrlKeyDown: function (event) {
			if (event.which !== ENTER_KEY) {
				return;
			}

			var val = this.refs.newField.getDOMNode().value.trim();
			if (val) {
				var newUrl = {
					url: val
				}
				var url = new Url();
				var self = this;
				url.save(newUrl, {
					success: function(url){
						self.props.urls.add(url);
					}
				});
				

				this.refs.newField.getDOMNode().value = '';
			}

			return false;
		},

		toggleAll: function (event) {
			var checked = event.target.checked;
			this.props.todos.forEach(function (todo) {
				todo.set('completed', checked);
			});
		},

		edit: function (todo, callback) {
			// refer to todoItem.jsx `handleEdit` for the reason behind the callback
			this.setState({editing: todo.get('id')}, callback);
		},

		save: function (todo, text) {
			todo.save({title: text});
			this.setState({editing: null});
		},

		cancel: function () {
			this.setState({editing: null});
		},

		clearCompleted: function () {
			this.props.todos.completed().forEach(function (todo) {
				todo.destroy();
			});
		},

		render: function () {
			var footer;
			var main;
			var urls = this.props.urls;

			var urlItems = urls.map(function (url) {
				return (
					<UrlItem
						key={url.get('id')}
						url={url}
						onToggle={url.toggleShowDetails.bind(url)}
						onDestroy={url.destroy.bind(url)}
						onEdit={this.edit.bind(this, url)}
						editing={this.state.editing === url.get('id')}
						onSave={this.save.bind(this, url)}
						onCancel={this.cancel}
					/>
				);
			}, this);

			if (urls.length) {
				var firstPage = urls.getPage('first');
				var prevPage = urls.getPage('prev');
				var nextPage = urls.getPage('next');
				var lastPage = urls.getPage('last');

				main = (
					<section id="main">
						<ul id="url-list">
							{urlItems}
						</ul>
						<a className={"button two columns "+React.addons.classSet({
									'button-primary': firstPage
								})} href={firstPage?firstPage.href:'javascript:void(0)'}>&#124;&#60;</a>
						<a className={"button two columns "+React.addons.classSet({
									'button-primary': prevPage
								})} href={prevPage?prevPage.href:'javascript:void(0)'}>&#60;</a>
						<a className={"button two columns "+React.addons.classSet({
									'button-primary': nextPage
								})} href={nextPage?nextPage.href:'javascript:void(0)'} >&#62;</a>
						<a className={"button two columns "+React.addons.classSet({
									'button-primary': lastPage
								})} href={lastPage?lastPage.href:'javascript:void(0)'}>&#62;&#124;</a>
					</section>
				);
			}

			return (
				<div>
					<div className="row">
						<div className="twelve columns">
							<header id="header">
								<h1>Shorten URLs</h1>
							</header>
						</div>
					</div>

					<div className="row">
						<div className="eleven columns">
							<input 
								className="u-full-width" 
								type="text"
								ref="newField"
								placeholder="http://example.com" 
								id="new-url"
								onKeyDown={this.handleNewUrlKeyDown}
								autoFocus={true} />
						</div>
						<div className="one columns">
							<button className="button-primary" type="submit">Shorten!</button>
						</div>
					</div>

					<div className="row">
						<div className="twelve columns">
							{main}
						</div>
					</div>
				</div>
			);
		}
	});

	React.renderComponent(
		<TodoApp urls={app.urls} />,
		document.getElementById('app')
	);

})();
