import fs = module('fs');
import connect = module('../Sources/SewebarConnectClient/Client');

var scenarios = process.cwd() + '/scenarios';
var client = connect.createClient({
    url: 'http://localhost'
});

fs.readFile(scenarios + '/02/registration.xml', 'utf8', function (err,data) {
    if (err) {
        console.log(err);
    }

    client.register(data, {}, function(err, miner) {
        if (err) {
            console.log(err);
        } else {
            fs.readFile(scenarios + '/02/Import3.xml', 'utf8', function (err, data) {
                miner.init(data, function (err) {
                    if (err) {
                        console.log(err);
                    } else {
                        fs.readFile(scenarios + '/02/ETReeMiner.Task52.xml', 'utf8', function (err, data) {
                            miner.runTask(data, function (e, r) {
                                console.log(e || r);
                            });
                        });
                    }
                });
            });
        }
    });
});