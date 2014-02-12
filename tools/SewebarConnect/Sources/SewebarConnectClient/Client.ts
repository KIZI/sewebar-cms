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

        public get(id: string, callback: (err: any, miner?: Miner) => void ): void {
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

        public init(dictionary: string, callback: (err) => void ) {
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

        public runTask(task: string, callback: (err, results?) => void): void {
            this.run('task', task, callback);
        }

        public runGrid(task: string, callback: (err, results?) => void): void {
            this.run('grid', task, callback);
        }

        public runProc(task: string, callback: (err, results?) => void): void {
            this.run('proc', task, callback);
        }

        private run(taskType: string, task: string, callback: (err, results?) => void ): void {
            // miners/{minerId}/tasks/{taskType}{?alias,template}
            var url = [
                this.server,
                'miners/',
                this.id,
                '/tasks/',
                taskType
            ].join('');

            this.opts.path = url;

            this.client.restClient.post(this.opts, task, (e, req, res, data) => {
                if (e) {
                    errorHandler(e, 'POST ' + url, callback);
                } else if (callback && typeof callback === 'function') {
                    callback(null, data);
                }
            });
        }

        public cancelTask(task: string, callback: (err, results?) => void): void {
            this.cancel('task', task, callback);
        }

        public cancelGrid(task: string, callback: (err, results?) => void): void {
            this.cancel('grid', task, callback);
        }

        public cancelProc(task: string, callback: (err, results?) => void): void {
            this.cancel('proc', task, callback);
        }

        public cancelAll(callback: (err, results?) => void ): void {
            this.cancel(null, null, callback);
        }

        private cancel(taskType: string, task: string, callback: (err, results?) => void ) {
            // PUT miners/{minerId}/tasks/{taskType}/{taskName}
            var data,
                url = [
                    this.server,
                    'miners/',
                    this.id,
                    '/tasks'
                ];

            if (taskType) {
                url.push('/', taskType, '/', task);
            }
            
            this.opts.path = url.join('');

            data = xml('CancelationRequest', {});

            this.client.restClient.put(this.opts, data, (e, req, res, data) => {
                if (e) {
                    errorHandler(e, 'PUT ' + url, callback);
                } else if (callback && typeof callback === 'function') {
                    callback(null, data);
                }
            });
        }

        public getTask(taskName: string, alias: string, template: string, callback: (err: any, results?: string) => void ): void {
            if (typeof alias === 'function') {
                callback = <any>alias;
                alias = '';
                template = '';
            }

            // miners/{minerId}/tasks/{taskType}/{taskName}{?alias,template}
            var url = [
                this.server,
                'miners/',
                this.id,
                '/tasks/',
                '/',
                taskName,
                '?alias=', alias,
                '&template=', template
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

        public getAllTasks(callback: (err: any, results?: string) => void ): void {
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

        public getDataDictionary(matrix: string, template: string, callback: (err: any, dictionary?: string) => void): void {
            if (typeof matrix === 'function') {
                callback = <any>matrix;
                matrix = '';
                template = '';
            }

            // GET miners/{minerId}/DataDictionary{?matrix,template}
            var url = [
                this.server,
                'miners/',
                this.id,
                '/DataDictionary',
                '?matrix=', matrix,
                '&template=', template
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
}
