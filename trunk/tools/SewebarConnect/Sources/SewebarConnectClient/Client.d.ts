declare module 'SewebarConnect' {
    export function createClient(cfg): SewebarConnectClient;

    export class SewebarConnectClient {
        constructor(cfg: Object);
        register(connection: DbConnection, metabase: DbConnection, callback: (err: any, miner: Miner) => void ): void;
        get(id: string, callback: (err: any, miner: Miner) => void );
    }

    export class Miner {
        id: string;

        init(dictionary: string, callback: (err: any) => void ): void;
        runTask(task: string, callback: (err: any, results: any) => void ): void;
        runGrid(task: string, callback: (err, results) => void ): void;
        runProc(task: string, callback: (err, results) => void ): void;
        cancelTask(task: string, callback: (err: any, results: any) => void ): void;
        cancelGrid(task: string, callback: (err: any, results: any) => void ): void;
        cancelProc(task: string, callback: (err: any, results: any) => void ): void;
        cancelAll(callback: (err, results) => void ): void;
        getTask(taskName: string, alias: string, template: string, callback: (err: any, results: string) => void ): void;
        getTask(taskName: string, callback: (err: any, results: string) => void ): void;
        getAllTasks(callback: (err: any, results: string) => void ): void;
        getDataDictionary(matrix: string, template: string, callback: (err: any, dictionary: string) => void );
        getDataDictionary(callback: (err: any, dictionary: string) => void );
        remove(callback: (err: any) => void ): void;
    }

    export interface DbConnection {
        type: string;
        file?: string;
        server?: string;
        database?: string;
        username?: string;
        password?: string;
    }
}
