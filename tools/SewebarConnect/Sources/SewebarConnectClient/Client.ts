///<reference path='node.d.ts'/>

import http = require('http');
import restify = require('restify');
import data2xml = require('data2xml');
import xml2js = require('xml2js');
import S = require('string');

export module SewebarConnect {
    var parser = new xml2js.Parser(),
        xml = data2xml();

    export function createClient(cfg): SewebarConnectClient {
        return new SewebarConnectClient(cfg);
    }

    function errorHandler(err, info, callback: (err, ...params: any[]) => void ) {
        // TODO: error when trying to reach nonexising server
        var e = err && err.body ? err.body : '',
            error = S(e).trim().s;

        xml2js.parseString(error, (parseError, result) => {
            var message = err.statusCode || 'Uknonwn error - ' + info;

            if(parseError) {
                message = err.message || err || parseError;
            } else {
                message = result && result.response ? result.response.message[0] : message;
            }

            if (callback && typeof callback === 'function') {
                callback(message, null);
            }
        });
    }

    function getAnonymousUser(): { name: string; password: string } {
        return {
            name: 'anonymous',
            password: ''
        };
    }

    export class SewebarConnectClient {
        restClient: restify.StringClient;
        opts: any;
        server: string;

        constructor(cfg: Object) {
            var options = {
                host: "127.0.0.1",
                port: 8888,
                path: 'url',
                headers: {
                    Host: 'localhost'
                }
            };

            var user = getAnonymousUser();

            // TODO: avoid changing cfg itself
            this.opts = cfg;
            this.server = this.opts.app == null ? 'SewebarConnect' : this.opts.app;

            if (this.server.substring(0, 1) !== '/') {
                this.server = '/' + this.server;
            }

            if (this.server.slice(-1) !== '/') {
                this.server = this.server + '/';
            }

            delete this.opts.app;

            this.restClient = restify.createStringClient(this.opts);
            this.restClient.basicAuth(user.name, user.password);
        }

        public register(connection: DbConnection, metabase: DbConnection, callback: (err, miner?: Miner) => void): void {
            // POST miners
            var mb, database, data, raw,
                url = [
                    this.server,
                    'miners'
                ].join('');
            
            if (connection.type.toLocaleLowerCase() == 'access') {
                database = {
                    _attr: { type: connection.type },
                    File: connection.file
                };
            } else {
                database = {
                    _attr: { type: connection.type },
                    Server: connection.server,
                    Database: connection.database,
                    Username: connection.username,
                    Password: connection.password
                };
            }

            if (metabase) {
                if (metabase.type.toLocaleLowerCase() == 'access') {
                    mb = {
                        _attr: { type: metabase.type },
                        File: metabase.file
                    };
                } else if (metabase.type.toLocaleLowerCase() == 'mysql') {
                    mb = {
                        _attr: { type: metabase.type },
                        Server: metabase.server,
                        Database: metabase.database,
                        Username: metabase.username,
                        Password: metabase.password
                    };
                }
            }

            raw = {
                Connection: database
            };

            if (mb) {
                raw.Metabase = mb;
            }

            data = xml('RegistrationRequest', raw);

            this.opts.path = url;

            this.restClient.post(this.opts, data, (err, req, res, body) => {
                var xml = S(body).trim().s;

                parser.parseString(xml, (parseErr, result) => {
                    if (!parseErr && result.response['$'].status === 'success') {
                        if (callback && typeof callback === 'function') {
                            callback(null, new Miner(result.response['$'].id, this));
                        }
                    } else {
                        errorHandler(err, 'POST ' + url, callback);
                    }
                });
            });
        }

        public getMiner(id: string, callback: (err: any, miner?: Miner) => void ): void {
            // GET miners/{minerId}
            var url = [
                this.server,
                'miners/',
                encodeURIComponent(id)
            ].join('');

            this.restClient.get(url, (err, req, res, body) => {
                var xml = S(body).trim().s;

                parser.parseString(xml, (err, result) => {
                    if (!err && result.response['$'].status === 'success') {
                        if (callback && typeof callback === 'function') {
                            callback(null, new Miner(result.response['$'].id, this));
                        }
                    } else {
                        errorHandler(err, 'POST ' + url, callback);
                    }
                });
            });
        }
    }

    export class Miner {
        private server: string;
        private opts: any;

        public get id() {
            return this.miner_id;
        }

        constructor(private miner_id: string, private client: SewebarConnectClient) {
            this.server = this.client.server;
            this.opts = this.client.opts;
        }

        public init(dictionary: string, callback: (err) => void): void {
            // PUT miners/{minerId}/DataDictionary
            var url = [
                this.server,
                'miners/',
                encodeURIComponent(this.id),
                '/',
                'DataDictionary'
            ].join('');

            this.opts.path = url;

            this.client.restClient.put(this.opts, dictionary, (e, req, res, data) => {
                if (e) {
                    errorHandler(e, 'PUT ' + url, callback);
                } else {
                    if (callback && typeof callback === 'function') {
                        callback(null);
                    }
                }
            });
        }

        public runTask(task: string, callback: (err, results?) => void): void;
        public runTask(task: TaskOptions, callback: (err, results?) => void): void;
        public runTask(task: any, callback: (err, results?) => void): void {
            var opts: TaskOptions;

            if (typeof task === 'string') {
                opts = {
                    type: 'task',
                    definition: task
                };
            } else {
                opts = task;
            }

            // miners/{minerId}/tasks/{taskType}{?alias,template}
            var url = [
                this.server,
                'miners/',
                this.id,
                '/tasks/',
                opts.type,
                '?alias=', (opts.alias || ''),
                '&template=', (opts.template || '')
            ].join('');

            this.opts.path = url;

            this.client.restClient.post(this.opts, opts.definition, (e, req, res, data) => {
                if (e) {
                    errorHandler(e, 'POST ' + url, callback);
                } else if (callback && typeof callback === 'function') {
                    callback(null, data);
                }
            });
        }

        public cancelTask(task: Task, callback: (err, results?) => void): void;
        public cancelTask(task: string, callback: (err, results?) => void): void;
        public cancelTask(task: any, callback: (err, results?) => void): void {
            var opts: Task,
                url: string[],
                data = xml('CancelationRequest', {});

            if (typeof task === 'string') {
                opts = {
                    type: 'task',
                    name: task
                };
            } else {
                opts = task;
            }

            // PUT miners/{minerId}/tasks/{taskType}/{taskName}
            url = [
                this.server,
                'miners/',
                this.id,
                '/tasks',
                '/', opts.type, '/',
                opts.name
            ];

            this.opts.path = url.join('');

            this.client.restClient.put(this.opts, data, (e, req, res, d) => {
                if (e) {
                    errorHandler(e, 'PUT ' + url, callback);
                } else if (callback && typeof callback === 'function') {
                    callback(null, d);
                }
            });
        }

        public cancelAll(type: string, callback: (err, results?) => void ): void {
            this.cancelTask({
                type: type || 'task'
            }, callback);
        }

        public getTask(task: string, callback: (err: any, results?: string) => void ): void;
        public getTask(task: TaskOptions, callback: (err: any, results?: string) => void ): void;
        public getTask(task: any, callback: (err: any, results?: string) => void ): void {
            var opts: TaskOptions;

            if (typeof task === 'string') {
                opts = {
                    type: 'task',
                    name: task
                };
            } else {
                opts = task;
            }

            // miners/{minerId}/tasks/{taskType}/{taskName}{?alias,template}
            var url = [
                this.server,
                'miners/',
                this.id,
                '/tasks/',
                '/',
                opts.name,
                '?alias=', (opts.alias || ''),
                '&template=', (opts.template || '')
            ].join('');

            this.opts.path = url;

            this.client.restClient.get(this.opts, (e, req, res, data) => {
                if (e) {
                    errorHandler(e, 'GET ' + url, callback);
                } else if (callback && typeof callback === 'function') {
                    callback(null, data);
                }
            });
        }

        public getAllTasks(callback: (err: any, results?: string) => void): void {
            // GET miners/{minerId}/tasks
            var url = [
                this.server,
                'miners/',
                this.id,
                '/tasks',
            ].join('');

            this.opts.path = url;

            this.client.restClient.get(this.opts, (e, req, res, data) => {
                if (e) {
                    errorHandler(e, 'GET ' + url, callback);
                } else if (callback && typeof callback === 'function') {
                    callback(null, data);
                }
            });
        }

        public remove(callback: (err: any) => void): void {
            var url = [
                this.server,
                'miners/',
                this.id
            ].join('');

            this.opts.path = url;

            this.client.restClient.del(this.opts, (e, req, res, data) => {
                if (e) {
                    errorHandler(e, 'DELETE ' + url, callback);
                } else if (callback && typeof callback === 'function') {
                    callback(null);
                }
            });
        }

        public getDataDictionary(dd: string, callback: (err: any, dictionary?: string) => void): void;
        public getDataDictionary(dd: DataDictionaryOptions, callback: (err: any, dictionary?: string) => void): void;
        public getDataDictionary(dd: any, callback: (err: any, dictionary?: string) => void): void {
            var opts: DataDictionaryOptions,
                url: string;

            if (typeof dd === 'string') {
                opts = {
                    matrix: dd
                };
            } else {
                opts = dd;
            }

            // GET miners/{minerId}/DataDictionary{?matrix,template}
            url = [
                this.server,
                'miners/',
                this.id,
                '/DataDictionary',
                '?matrix=', (opts.matrix || ''),
                '&template=', (opts.template || '')
            ].join('');

            this.opts.path = url;

            this.client.restClient.get(this.opts, (e, req, res, data) => {
                if (e) {
                    errorHandler(e, 'GET ' + url, callback);
                } else if (callback && typeof callback === 'function') {
                    callback(null, data);
                }
            });
        }
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
