///<reference path='node.d.ts'/>

import fs = module('fs');
import restify = module('restify');

var server = '/SewebarConnect';

var client = restify.createStringClient({
    url: 'http://192.168.23.108'
});

fs.readFile('scenarios/registration.txt', 'utf8', function (err,data) {
    if (err) {
        console.log(err);
    }

    client.post(server + '/Application/Register', data, function(err, req, res, data) {
        console.log('%s', data);
    });
});