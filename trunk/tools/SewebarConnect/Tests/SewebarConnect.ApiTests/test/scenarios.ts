///<reference path='../node.d.ts'/>
///<reference path='../mocha.d.ts'/>
///<reference path='../should.d.ts'/>
///<reference path='../node_modules/SewebarConnect/Client.d.ts'/>

import connect = module('SewebarConnect');
import fs = module('fs');
import should = module('should');

var scenarios = process.cwd() + '/scenarios';

describe('SewebarConnect', function() {
    describe('Scenario 02', function() {
        var client: connect.SewebarConnectClient,
            miner: connect.Miner,
            dataDictionary = '',
            task = '',
            config;

        before((done) => {
            fs.readFile(process.cwd() + '/config.json', 'utf8', (err, data) => {
                should.not.exist(err);

                config = JSON.parse(data);

                client = connect.createClient(config);

                dataDictionary = fs.readFileSync(scenarios + '/02/Import3.xml', 'utf8');
                task = fs.readFileSync(scenarios + '/02/ETReeMiner.Task52.xml', 'utf8');

                done();
            });
        });

        it('should successfully register', (done) => {
            var database: connect.DbConnection,
                metabase: connect.DbConnection;

            database = {
                type: 'Access',
                file: 'Barbora.mdb'
            };

            metabase = {
                type: 'Access',
                file: 'LM Barbora.mdb'
            }

            client.register(database, metabase, (err, m) => {
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

        it.skip('should export existing task', (done) => {
            done();
        });

        it.skip('should cancel running task', (done) => {
            done();
        });

        it('should get datadictionary with empty string as matrix and template', (done) => {
            miner.getDataDictionary('', '', (err, dd) => {
                should.not.exist(err);
                should.exist(dd);

                done();
            });
        });

        it('should get existing miner', (done) => {
            var client2 = connect.createClient(config);

            client2.get(miner.id, (err, m: connect.Miner) => {
                should.not.exist(err);

                should.exist(m);
                (<any>m.id).should.eql(miner.id);

                done();
            });
        });

        it.skip('should remove miner', (done) => {
            miner.remove((err) => {
                should.not.exist(err);

                done();
            });
        });
    });
});