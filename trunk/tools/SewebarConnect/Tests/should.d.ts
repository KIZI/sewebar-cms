declare module 'should' {
    export var not;
    export var be;

    declare function exist(p: any);
    declare function eql(p: any);

    declare class Assertion {
    }
}