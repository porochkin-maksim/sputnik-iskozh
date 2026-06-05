import routes from '../routes.json';

const hasQueryValue = (value) => {
    if (!value) {
        return false;
    }

    return String(value) !== '0';
};

export function routeUri(routeName, routeArgs = {}, queryParams = {}) {
    const route = routes[routeName];

    if (!route?.uri) {
        throw new Error(`Unknown route: ${routeName}`);
    }

    let uri = route.uri;

    Object.entries(routeArgs).forEach(([key, value]) => {
        uri = uri.replace(`{${key}}`, value ?? '');
    });

    const searchParams = new URLSearchParams();

    Object.entries(queryParams).forEach(([key, value]) => {
        if (!hasQueryValue(value)) {
            return;
        }

        if (Array.isArray(value)) {
            value.forEach(item => {
                if (hasQueryValue(item)) {
                    searchParams.append(`${key}[]`, item);
                }
            });

            return;
        }

        searchParams.append(key, value);
    });

    const query = searchParams.toString();

    return query ? `${uri}?${query}` : uri;
}

export function routeMeta(routeName) {
    const route = routes[routeName];

    if (!route?.uri || !route?.method) {
        throw new Error(`Unknown route: ${routeName}`);
    }

    return route;
}
