declare module 'restify' {
    export declare class StringClient {
        get(resource: string, handler: (err, req, res, data) => void);
        post(resource: string, data: Object, handler: (err, req, res, data) => void);
    }

    export declare function createStringClient(cfg: Object): StringClient;
}