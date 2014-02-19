declare module 'SewebarConnect' {
    export function createClient(cfg): SewebarConnectClient;

    export class SewebarConnectClient {
        constructor(cfg: Object);
        register(connection: DbConnection, metabase: DbConnection, callback: (err: any, miner: Miner) => void): void;
        get(id: string, callback: (err: any, miner: Miner) => void);
    }

    export class Miner {
        id: string;

        init(dictionary: string, callback: (err: any) => void ): void;

        runTask(task: string, callback: (err: any, results: any) => void): void;
        runTask(task: TaskOptions, callback: (err: any, results: any) => void): void;

        cancelTask(task: string, callback: (err: any, results: any) => void): void;
        cancelTask(task: Task, callback: (err: any, results: any) => void): void;
        cancelAll(type: string, callback: (err: any, results: any) => void): void;

        getTask(task: string, callback: (err: any, results: string) => void): void;
        getTask(task: TaskOptions, callback: (err: any, results: string) => void): void;
        getAllTasks(callback: (err: any, results: string) => void): void;

        getDataDictionary(dd: string, callback: (err: any, dictionary: string) => void): void;
        getDataDictionary(dd: DataDictionaryOptions, callback: (err: any, dictionary: string) => void): void;

        remove(callback: (err: any) => void): void;
    }

    export interface DbConnection {
        type: string;
        file?: string;
        server?: string;
        database?: string;
        username?: string;
        password?: string;
    }

    export interface Task {
        type: string;
        name?: string;
        definition?: string;
    }

    export interface TaskOptions extends Task {
        alias?: string;
        template?: string;
    }

    export interface DataDictionaryOptions {
        matrix: string;
        template?: string;
    }
}
