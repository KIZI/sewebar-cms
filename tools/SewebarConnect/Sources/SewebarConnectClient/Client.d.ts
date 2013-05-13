declare module 'SewebarConnect' {
    export declare function createClient(cfg): SewebarConnectClient;

    export declare class SewebarConnectClient {
        constructor(cfg: Object);
        register(connection, metabase, callback: (err: any, miner: Miner) => void): void;
        get(id: string, callback: (err: any, miner: Miner) => void);
    }

    export class Miner {
        id: string;

        init(dictionary: string, callback: (err: any) => void): void;
        runTask(task: string, callback: (err: any, results: any) => void): void;
        runGrid(task: string, callback: (err, results) => void): void;
        runProc(task: string, callback: (err, results) => void): void;
        cancelTask(task: string, callback: (err: any, results: any) => void): void;
        cancelGrid(task: string, callback: (err: any, results: any) => void): void;
        cancelProc(task: string, callback: (err: any, results: any) => void): void;
        getTask(taskName: string, callback: (err: any, results: any) => void): void;
        getDataDictionary(matrix: string, template: string, callback: (err: any, dictionary: string) => void);
        remove(callback: (err: any) => void): void;
    }
}
