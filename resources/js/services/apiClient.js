const tokenKey = 'issue_ticketing_access_token';
const userKey = 'issue_ticketing_user';
const loginPath = '/login';

export const getToken = () => localStorage.getItem(tokenKey);

export const getStoredUser = () => {
    const user = localStorage.getItem(userKey);

    if (!user) {
        return null;
    }

    try {
        return JSON.parse(user);
    } catch {
        return null;
    }
};

export const storeSession = ({ accessToken, user }) => {
    localStorage.setItem(tokenKey, accessToken);
    localStorage.setItem(userKey, JSON.stringify(user));
};

export const clearSession = () => {
    localStorage.removeItem(tokenKey);
    localStorage.removeItem(userKey);
};

export const redirectToLogin = () => {
    clearSession();

    if (window.location.pathname !== loginPath) {
        window.location.href = loginPath;
    }
};

export const apiRequest = async (path, options = {}) => {
    const headers = {
        Accept: 'application/json',
        ...(options.headers ?? {}),
    };

    const token = getToken();

    if (token) {
        headers.Authorization = `Bearer ${token}`;
    }

    if (options.body && !(options.body instanceof FormData)) {
        headers['Content-Type'] = 'application/json';
    }

    const response = await fetch(`/api${path}`, {
        ...options,
        headers,
        body: options.body && !(options.body instanceof FormData) ? JSON.stringify(options.body) : options.body,
    });

    const payload = await response.json().catch(() => ({
        success: false,
        message: 'Unexpected response from server',
        errors: null,
    }));

    if (response.status === 401) {
        redirectToLogin();
        throw payload;
    }

    if (!response.ok || payload.success === false) {
        throw payload;
    }

    return payload;
};
