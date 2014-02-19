declare module "data2xml" {
    var data2xml: {
        (opts?: any): (name: string, data: Object) => string;
    };

    export= data2xml;
}