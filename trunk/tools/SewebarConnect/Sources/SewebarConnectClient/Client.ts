///<reference path='node.d.ts'/>
///<reference path='restify.d.ts'/>

import restify = module('restify');
import http = module('http');
var request = require('request');
var xml2js = require('xml2js');
var S = require('string');

export module SewebarConnect {
    var parser = new xml2js.Parser();

    export function createClient(cfg): SewebarConnectClient {
        return new SewebarConnectClient(cfg);
    }

    function errorHandler(err, info, callback: (err, ...params: any[]) => void) {
        var e = err ? err.body : '',
            error = S(e).trim().s;

        xml2js.parseString(error, (parseError, result) => {
            var message = 'Uknonwn error - ' + info;

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
        }

        public register(connection, metabase, callback: (err, miner: Miner) => void): void {
            // TODO: create correct data object
            var data = connection,
                url = this.server + 'miners';

            this.opts.path = url;

            this.restClient.post(this.opts, data, (err, req, res, body) => {
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

        public get (id: string, callback: (err: any, miner: Miner) => void ): void {
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
            // PATCH miners/{minerId}
            var url = [
                this.server,
                'miners/',
                encodeURIComponent(this.id)
            ].join('');

            this.opts.path = url;

            this.client.restClient.patch(this.opts, dictionary, (e, req, res, data) => {
                if (e) {
                    errorHandler(e, 'PATCH ' + url, callback);
                } else {
                    if (callback && typeof callback === 'function') {
                        callback(null);
                    }
                }
            });
        }

        public runTask(task: string, callback: (err, results) => void): void {
            this.run('task', task, callback);
        }

        public runGrid(task: string, callback: (err, results) => void): void {
            this.run('grid', task, callback);
        }

        public runProc(task: string, callback: (err, results) => void): void {
            this.run('proc', task, callback);
        }

        private run(taskType: string, task: string, callback: (err, results) => void ): void {
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

        public cancelTask(task: string, callback: (err, results) => void): void {
            this.cancel('task', task, callback);
        }

        public cancelGrid(task: string, callback: (err, results) => void): void {
            this.cancel('grid', task, callback);
        }

        public cancelProc(task: string, callback: (err, results) => void): void {
            this.cancel('proc', task, callback);
        }

        private cancel(taskType: string, task: string, callback: (err, results) => void) {
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

        public getTask(taskName: string, alias: string, template: string, callback: (err: any, results: string) => void ): void {
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

        public getAllTasks(callback: (err: any, results: string) => void ): void {
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

        public getDataDictionary(matrix: string, template: string, callback: (err: any, dictionary: string) => void): void {
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
}