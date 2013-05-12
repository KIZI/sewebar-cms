///<reference path='node.d.ts'/>
///<reference path='restify.d.ts'/>

import restify = module('restify');
import http = module('http');
var request = require('request');
var xml2js = require('xml2js');
var S = require('string');

export module SewebarConnect {
    var parser = new xml2js.Parser();

    export function createClient(cfg) {
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

        register(connection, metabase, callback: (err, miner: Miner) => void) {
            // TODO: create correct data object
            var data = connection,
                url = this.server + 'miners';

            this.opts.path = url;

            this.restClient.post(this.opts, data, (err, req, res, body) => {
                console.log(res);
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

        public get(id: string, callback: (err: any, miner: Miner) => void) {
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

        init(dictionary: string, callback: (err) => void) {
            var url = [
                this.server,
                'miners/',
                this.id
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

        runTask(task: string, callback: (err, results) => void) {
            var url = [
                this.server,
                'miners/',
                this.id,
                '/tasks/',
                'task'
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
    }
}
