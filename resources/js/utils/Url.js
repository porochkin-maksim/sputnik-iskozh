import Routes              from '../routes.json';
import * as RouteFunctions from '../routes-functions';

let Generator = {
    /**
     * @param routeObject Routes.<routeName>
     * @param params Object like {...}
     * @param getParams Object like {key => value}
     *
     * @returns string
     */
    makeUri (routeObject, params = {}, getParams = {}) {
        let uri = routeObject.uri;
        Object.keys(routeObject.args).forEach(key => {
            uri = uri.replace(routeObject.args[key], params[key] ? params[key] : '');
        });
        let getQuery = [];
        Object.keys(getParams).forEach(key => {
            if (getParams[key] && String(getParams[key]) !== '0') {
                getQuery = getQuery.concat([key + '=' + getParams[key]]);
            }
        });
        if (getQuery.length) {
            uri = uri + '?' + getQuery.join('&');
        }
        return uri;
    },
};

export default {
    Routes,
    Generator,
    RouteFunctions,
};
