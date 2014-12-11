/**
 * @jsx React.DOM
 */
/*jshint quotmark: false */
/*jshint white: false */
/*jshint trailing: false */
/*jshint newcap: false */
/*global React */
var app = app || {};

(function () {
	'use strict';

	var ESCAPE_KEY = 27;
	var ENTER_KEY = 13;

	app.UrlItem = React.createClass({
		render: function () {
			return (
				<div>
					<div className="row">
						<div className="ten columns">
							<a href={"http://www.s.batjaa.com/"+this.props.url.get('key')}>{this.props.url.get('key')}</a>
						</div>
						<div className="two columns">
							<button
								className={React.addons.classSet({
									'button-primary': this.props.url.get('showDetails')
								})}
								onClick={this.props.onToggle}>
								Show Details
							</button>
						</div>
					</div>
					<div className={"row " + React.addons.classSet({
						'u-show': !this.props.url.get('showDetails'),
						'u-hidden': !this.props.url.get('showDetails')
					})}>
						<div className="six columns">
							<table class="u-full-width">
								<tbody>
									<tr>
										<td><strong>Original URL:</strong></td>
										<td>{this.props.url.get('url')}</td>
									</tr>
									<tr>
										<td><strong>Created Date:</strong></td>
										<td>{this.props.url.get('created_date')}</td>
									</tr>
									<tr>
										<td><strong>Visits:</strong></td>
										<td>{this.props.url.get('urlVisits').length}</td>
									</tr>
								</tbody>
							</table>
						</div>
						<div className="six columns"></div>
					</div>
					<hr />
				</div>
			);
		}
	});

})();
