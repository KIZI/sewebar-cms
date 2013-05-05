///<reference path='../node.d.ts'/>
///<reference path='../mocha.d.ts'/>

var connect = require('../../Sources/SewebarConnectClient/Client');
var fs = require('fs');
var should = require('should');

var scenarios = process.cwd() + '/scenarios';

declare var should : {
    exist(o);
    not;
}

describe('SewebarConnect', function() {
	describe('Scenario 02', function() {
		var client,
            miner,
            resource = {},
		    dataDictionary = '',
            task = '';

		before(function() {
            client = connect.createClient({
                url: 'http://192.168.23.108'
            });

            resource = fs.readFileSync(scenarios + '/02/registration.xml', 'utf8');
            dataDictionary = fs.readFileSync(scenarios + '/02/Import3.xml', 'utf8');
            task = fs.readFileSync(scenarios + '/02/ETReeMiner.Task52.xml', 'utf8');
		});

        it('should successfully register', (done) => {
            client.register(resource, {}, (err, m) => {
                miner = m;

                should.not.exist(err);
                should.exist(miner);

                done();
            });
        });

		it('should successfully init', (done) => {
            miner.init(dataDictionary, (err) => {
                should.not.exist(err);

                done();
			});
		});

        it('should run task', (done) => {
            miner.runTask(task, (err, results) => {
                should.not.exist(err);

                results.should.be.ok;

                done();
            });
        });
	});
});