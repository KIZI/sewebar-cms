declare module 'SewebarConnect' {
    export declare function createClient(cfg): SewebarConnectClient;

    export declare class SewebarConnectClient {
        constructor(cfg: Object);
        public register(connection, metabase, callback: (err: any, miner: Miner) => void): void;
        public get(id: string, callback: (err: any, miner: Miner) => void);
    }

    export class Miner {
        public id: string;

        public init(dictionary: string, callback: (err: any) => void): void;
        public runTask(task: string, callback: (err: any, results: any) => void): void;
    }
}
