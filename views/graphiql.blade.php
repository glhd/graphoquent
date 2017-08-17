<!DOCTYPE html>
<html>
<head>
	<link rel="stylesheet" href="https://unpkg.com/graphiql@^0.11.2/graphiql.css" />
	<style>
	body {
		height: 100%;
		margin: 0;
		width: 100%;
		overflow: hidden;
	}
	
	#graphiql {
		height: 100vh;
	}
	</style>
	
	<script src="//cdn.jsdelivr.net/es6-promise/4.0.5/es6-promise.auto.min.js"></script>
	<script src="//cdn.jsdelivr.net/fetch/0.9.0/fetch.min.js"></script>
	<script src="//cdn.jsdelivr.net/react/15.4.2/react.min.js"></script>
	<script src="//cdn.jsdelivr.net/react/15.4.2/react-dom.min.js"></script>
	<script src="https://unpkg.com/graphiql@^0.11.2/graphiql.min.js"></script>
</head>
<body>

<div id="graphiql">
	Loading...
</div>

<script>
var search = window.location.search;
var parameters = {};
search.substr(1).split('&').forEach(function(entry) {
	var eq = entry.indexOf('=');
	if (eq >= 0) {
		parameters[decodeURIComponent(entry.slice(0, eq))] =
			decodeURIComponent(entry.slice(eq + 1));
	}
});

// Format variables
if (parameters.variables) {
	try {
		parameters.variables = JSON.stringify(JSON.parse(parameters.variables), null, 2);
	} catch (e) {
	}
}

function updateURL() {
	var newSearch = '?' + Object.keys(parameters).filter(function(key) {
		return Boolean(parameters[key]);
	}).map(function(key) {
		return encodeURIComponent(key) + '=' + encodeURIComponent(parameters[key]);
	}).join('&');
	history.replaceState(null, null, newSearch);
}

ReactDOM.render(
	React.createElement(GraphiQL, {
		fetcher: function(graphQLParams) {
			return fetch('/graphql', {
				method: 'post',
				headers: {
					'Accept': 'application/json',
					'Content-Type': 'application/json'
				},
				body: JSON.stringify(graphQLParams),
				credentials: 'include'
			}).then(function(response) {
				return response.text();
			}).then(function(responseBody) {
				try {
					return JSON.parse(responseBody);
				} catch (error) {
					return responseBody;
				}
			});
		},
		query: parameters.query,
		variables: parameters.variables,
		operationName: parameters.operationName,
		onEditQuery: function(newQuery) {
			parameters.query = newQuery;
			updateURL();
		},
		onEditVariables: function(newVariables) {
			parameters.variables = newVariables;
			updateURL();
		},
		onEditOperationName: function(newOperationName) {
			parameters.operationName = newOperationName;
			updateURL();
		}
	}),
	document.getElementById('graphiql')
);
</script>

</body>
</html>
