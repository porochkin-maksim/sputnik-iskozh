import Routes from '../routes.json';

let Generator = {
	/**
	 * @param routeObject Routes.<routeName>
	 * @param params Object like {...}
	 *
	 * @returns string
	 */
	makeUri(routeObject, params = {}) {
		let uri = routeObject.uri;
		Object.keys(routeObject.args).forEach(key => {
			uri = uri.replace(routeObject.args[key], params[key] ? params[key] : '');
		});
		return uri;
	},
};

export default {
	Routes,
	Generator,
};