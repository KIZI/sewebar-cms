declare module 'restify' {
    export class HttpClient {
        get(resource: string, handler: (err, req, res, data) => void);
        get(options: Object, handler: (err, req, res, data) => void);

        post(resource: string, data: Object, handler: (err, req, res, data) => void);
        post(options: Object, data: Object, handler: (err, req, res, data) => void);

        put(resource: string, data: Object, handler: (err, req, res, data) => void);
        put(options: Object, data: Object, handler: (err, req, res, data) => void);

        del(resource: string, handler: (err, req, res, data) => void);
        del(options: Object, handler: (err, req, res, data) => void);

        patch(resource: string, data: any, handler: (err, req, res, data) => void);
        patch(options: Object, data: any, handler: (err, req, res, data) => void);
    }

    export function createStringClient(cfg: Object): StringClient;

    export function createJsonClient(cfg: Object): JsonClient;

    export class JsonClient extends HttpClient {
    }

    export class StringClient extends HttpClient {
    }
}