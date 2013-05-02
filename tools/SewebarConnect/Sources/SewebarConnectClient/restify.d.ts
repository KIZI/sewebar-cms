declare module 'restify' {
    export declare class StringClient {
        get(resource: string, handler: (err, req, res, data) => void);
        post(resource: string, data: any, handler: (err, req, res, data) => void);
        patch(resource: string, data: any, handler: (err, req, res, data) => void);
    }

    export declare function createStringClient(cfg: Object): StringClient;
}