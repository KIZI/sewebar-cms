var connect = require('../Sources/SewebarConnectClient/Client');
var fs = require('fs');
var should = require('should');

var scenarios = process.cwd() + '/scenarios';

describe('LISpMiner/I:ZI Miner', function() {
	describe('Scenario 02', function() {
		var resource = {},
		    dataDictionary = '',
            task = '';

		before(function() {
			resource = fs.readFileSync(scenarios + '/02/registration.xml', 'utf8');
            dataDictionary = fs.readFileSync(scenarios + '/02/Import3.xml', 'utf8');
            task = fs.readFileSync(scenarios + '/02/ETReeMiner.Task52.xml', 'utf8');
		});

		it('Process', function(done) {
            var client = connect.createClient({
                url: 'http://localhost'
            });

			client.register(resource, {}, function(err, miner){
				if(err) throw err;

                console.log(miner.id);

				miner.should.be.ok;

				miner.init(dataDictionary, function(err) {
					if(err) throw err;

					miner.runTask(task, function(err, results) {
						if(err) throw err;

						results.should.be.ok;

						done();
					});
				});
			});
		});
	});
});