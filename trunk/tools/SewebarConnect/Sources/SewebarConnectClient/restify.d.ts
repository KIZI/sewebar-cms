declare module 'restify' {
    export declare class HttpClient {
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

    export declare function createStringClient(cfg: Object): StringClient;

    export declare function createJsonClient(cfg: Object): JsonClient;

    export declare class JsonClient extends HttpClient {
    }

    export declare class StringClient extends HttpClient {
    }
}