declare module "xml2js" {
    export function parseString(str, options: Options, cb: cb): void;
    export function parseString(str, cb: cb): void;

    export class Parser {
        parseString(str: string, cb: cb): void;
    }

    interface cb {
        (err, result): void;
    }

    interface Options extends Object {
    }
}