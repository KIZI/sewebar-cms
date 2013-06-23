///<reference path='../node.d.ts'/>
///<reference path='../mocha.d.ts'/>
///<reference path='../should.d.ts'/>
///<reference path='../node_modules/SewebarConnect/Client.d.ts'/>

import connect = module('SewebarConnect');
import fs = module('fs');
import should = module('should');

describe('SewebarConnect', () => {
    var client: connect.SewebarConnectClient,
        miner: connect.Miner,
        dataDictionary = '',
        task = '',
        config,
        scenariosApiary = process.cwd() + '/scenarios/apiary',
        outputRootDir = process.cwd() + '/output',
        outputDir = process.cwd() + '/output/apiary';

    before((done) => {
        fs.readFile(process.cwd() + '/config.json', 'utf8', (err, data) => {
            should.not.exist(err);

            config = JSON.parse(data);

            client = connect.createClient(config);

            dataDictionary = fs.readFileSync(scenariosApiary + '/Import3.xml', 'utf8');
            task = fs.readFileSync(scenariosApiary + '/ETReeMiner.Task52.xml', 'utf8');

            if (!fs.existsSync(outputRootDir)) {
                fs.mkdirSync(outputRootDir);
            }

            if (!fs.existsSync(outputDir)) {
                fs.mkdirSync(outputDir);
            }

            done();
        });
    });

    describe('apiary compliance', () => {
        describe('LISp-Miners Management', () => {
            var out = outputDir + '/Management';

            before((done) => {
                if (!fs.existsSync(out)) {
                    fs.mkdirSync(out);
                }

                done();
            });

            it('#POST /miners', (done) => {
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

            it('#PUT /miners/{minerId}/DataDictionary', (done) => {
                miner.init(dataDictionary, (err) => {
                    should.not.exist(err);

                    done();
                });
            });

            it('#GET /miners/{minerId}/DataDictionary{?matrix,template}', (done) => {
                // default template and matrix
                miner.getDataDictionary('loans', 'LMDataSource.Matrix.ARD.Template.PMML', (err: string, dict: string) => {
                    should.not.exist(err);
                    should.exist(dict);

                    fs.writeFileSync(out + '/miner.getDataDictionary01.xml', dict);

                    // no template and matrix
                    miner.getDataDictionary((err2: string, dict2: string) => {
                        should.not.exist(err2);
                        should.exist(dict2);

                        fs.writeFileSync(out + '/miner.getDataDictionary02.xml', dict);

                        done();
                    });
                });
            });
        });

        describe('Tasks', () => {
            var out = outputDir + '/tasks';

            before((done) => {
                if (!fs.existsSync(out)) {
                    fs.mkdirSync(out);
                }

                done();
            });

            it('#GET /miners/{minerId}/tasks', (done) => {
                miner.getAllTasks((err, data) => {
                    should.not.exist(err);
                    should.exist(data);

                    fs.writeFileSync(out + '/miner.getAllTasks.xml', data);

                    done();
                });
            });

            it('#POST /miners/{minerId}/tasks/{taskType}{?alias,template}', (done) => {
                miner.runTask(task, (err, results) => {
                    should.not.exist(err);
                    should.exist(results);

                    done();
                });
            });

            it('#GET /miners/{minerId}/tasks/{taskName}{?alias,template}', (done) => {
                miner.getTask('9741046ed676ec7470cb043db2881a094e36b554', 'loans', 'ETreeMiner.Task.Template.PMML', (err, data) => {
                    should.not.exist(err);
                    should.exist(data);

                    fs.writeFileSync(out + '/miner.getTask01.xml', data);

                    miner.getTask('9741046ed676ec7470cb043db2881a094e36b554', (err, data) => {
                        should.not.exist(err);
                        should.exist(data);

                        fs.writeFileSync(out + '/miner.getTask02.xml', data);

                        done();
                    });
                });
            });

            it.skip('#GET /miners/{minerId}/tasks/{taskType}/{taskName}/definition', (done) => {
                done();
            });

            it('#PUT /miners/{minerId}/tasks/{taskType}/{taskName} (cancel task)', (done) => {
                miner.cancelTask('9741046ed676ec7470cb043db2881a094e36b554', (err, data) => {
                    should.not.exist(err);
                    should.exist(data);

                    fs.writeFileSync(out + '/miner.cancelTask01.xml', data);

                    done();
                });
            });

            it('#PUT /miners/{minerId}/tasks/{taskType}/{taskName} (cancel all tasks)', (done) => {
                miner.cancelTask(null, (err, data) => {
                    should.not.exist(err);
                    should.exist(data);

                    fs.writeFileSync(out + '/miner.cancelAllTasks01.xml', data);

                    done();
                });
            });

            it.skip('#DELETE /miners/{minerId}/tasks/{taskType}/{taskName}', (done) => {
                done();
            });
        });

        describe('User Management', () => {
            it.skip('#POST /users', (done) => {
                done();
            });

            it.skip('#GET /users/{userName}/{userPassword}?db={dbId}', (done) => {
                done();
            });
        });
    });

    /* after((done) => {
        // wait for tasks to finish
        // TODO: dont wait "random" time
        setTimeout(() => {
            // remove miner
            miner.remove((err) => {
                should.not.exist(err);

                done();
            });
        }, 4000);
    });*/
});
