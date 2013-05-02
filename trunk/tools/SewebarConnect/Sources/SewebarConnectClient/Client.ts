///<reference path='node.d.ts'/>
///<reference path='ISewebarConnectClient.d.ts'/>

import restify = module('restify');
var request = require('request');
var http = require("http");
var xml2js = require('xml2js');
var S = require('string');

var application = '/SewebarConnect';
var parser = new xml2js.Parser();

export function createClient(cfg) {
    return new SewebarConnectClient(cfg);
}

export interface IClient {
    register(connection, metabase, callback: (err, req, res, data) => void);
}

function errorHandler(err, info, callback: (err, ...params: any[]) => void) {
    var e = err ? err.body : '',
        error = S(e).trim().s;

    xml2js.parseString(error, function (parseError, result) {
        var message = 'Uknonwn error - ' + info;

        if(parseError) {
            message = err.message || err || parseError;
        } else {
            message = result && result.response ? result.response.message[0] : message;
        }

        if (callback && typeof callback === 'function') {
            callback(message, null);
        }
    })
}

export class SewebarConnectClient implements IClient {
    private restClient: restify.StringClient;
    private server = application;

    constructor(cfg: Object) {
        var options = {
            host: "127.0.0.1",
            port: 8888,
            path: 'url',
            headers: {
                Host: 'localhost'
            }
        };

        this.restClient = restify.createStringClient(cfg);
    }

    register(connection, metabase, callback: (err, id) => void) {
        var data = connection,
            url = this.server + '/miners';        

        this.restClient.post(url, data, (err, req, res, body) => {
            var xml = S(body).trim().s;

            parser.parseString(xml, (err, result) => {
                if (!err && result.response['$'].status === 'success') {
                    if (callback && typeof callback === 'function') {
                        callback(null, new Miner(result.response['$'].id, this.restClient));
                    }
                } else {
                    errorHandler(err, 'POST ' + url, callback);
                }
            });
        });
    }
}

export class Miner {
    private server = application;

    constructor(private id: string, private restClient: restify.StringClient) {

    }

    init(dictionary: string, callback: (err) => void) {
        var url = this.server + '/miners/' + this.id;

        this.restClient.patch(url, dictionary, function (e, req, res, data) {
            if (e) {
                errorHandler(e, 'PATCH ' + url, callback);
            } else {
                console.log(data);

                if (callback && typeof callback === 'function') {
                    callback(null);
                }
            }
        });
    }

    runTask(task: string, callback: (err, results) => void) {
        var url = [
            this.server,
            '/miners/',
            this.id,
            '/tasks/',
            'task'
        ].join('');

        this.restClient.post(url, task, function (e, req, res, data) {
            if (e) {
                errorHandler(e, 'POST ' + url, callback);
            } else if (callback && typeof callback === 'function') {
                callback(null, data);
            }
        });
    }
}
