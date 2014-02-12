declare module "string" {
    export = S;

    interface S {
        s: string;

        trim(): S;
    }

    function S(str: string): S;
}