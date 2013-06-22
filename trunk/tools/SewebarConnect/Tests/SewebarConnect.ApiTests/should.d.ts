declare module 'should' {
    export var not;
    export var be;

    export function exist(p: any);
    export function eql(p: any);

    export class Assertion {
    }
}