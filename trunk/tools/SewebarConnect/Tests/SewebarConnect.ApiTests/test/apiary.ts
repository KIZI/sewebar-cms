///<reference path='../node.d.ts'/>
///<reference path='../mocha.d.ts'/>
///<reference path='../should.d.ts'/>
///<reference path='../node_modules/SewebarConnect/Client.d.ts'/>

import connect = module('SewebarConnect');
import fs = module('fs');
import should = module('should');
import request = module('request');

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
            var uriBase,
                user = {
                    name: 'rnd',
                    password: 'heslo'
                },
                auth = 'Basic ' + new Buffer(user.name + ':' + user.password).toString('base64');

            before(() => {
                var server: string = config.app == null ? 'SewebarConnect' : config.app;

                if (server.substring(0, 1) !== '/') {
                    server = '/' + server;
                }

                if (server.slice(-1) === '/') {
                    server = server.slice(0, -1);
                }

                uriBase = config.url + server;
            });

            // registerUser
            it('#POST /users', (done) => {
                var uri = [uriBase, '/users'].join('');

                request.post(uri, (e, r, body) => {
                    should.not.exist(e);
                    should.exist(body);

                    r.statusCode.should.eql(200);

                    done();
                }).form(user);
            });
            
            it('#GET /users/{username}', (done) => {
                var uri = [uriBase, '/users/', user.name].join('');

                request.get({ url: uri, headers: { Authorization: auth }}, (e, r, body) => {
                    should.not.exist(e);
                    should.exist(body);

                    r.statusCode.should.eql(200);

                    done();
                });
            });

            // updateOtherUser
            // updateUser
            // confirmUserPasswordUpdate
            it.skip('#PUT /users/{username}', (done) => {
                done();
            });

            // deleteUser
            it('#DELETE /users/{username}', (done) => {
                var uri = [uriBase, '/users/', user.name].join('');

                request.del({ url: uri, headers: { Authorization: auth } }, (e, r, body) => {
                    should.not.exist(e);
                    should.exist(body);
                    
                    r.statusCode.should.eql(200);

                    done();
                });
            });

            describe('Databases', () => {
                var user2 = {
                    name: 'rnd2',
                    password: 'simple'
                }, database = {
                        db_id: 'db01',
                        db_password: 'secret'
                    }, auth2 = 'Basic ' + new Buffer(user2.name + ':' + user2.password).toString('base64');

                before((done) => {
                    var uri = [uriBase, '/users'].join('');

                    request.post(uri, (e, r, body) => {
                        done();
                    }).form(user2);
                });

                // registerUserDatabase
                it('#POST /users/{username}/databases', (done) => {
                    var uri = [uriBase, '/users/', user2.name, '/databases'].join('');

                    request.post({ url: uri, headers: { Authorization: auth2 } }, (e, r, body) => {
                        should.not.exist(e);
                        should.exist(body);
                        
                        r.statusCode.should.eql(200);

                        done();
                    }).form(database);
                });

                // getDatabasePassword
                it.skip('#GET /users/{username}/databases/{id}', (done) => {
                    done();
                });

                // setDatabasePassword
                it.skip('#GET /users/{username}/databases/{id}', (done) => {
                    done();
                });

                it.skip('#DELETE /users/{username}/databases/{id}', (done) => {
                    done();
                });

                after((done) => {
                    var uri = [uriBase, '/users/', user2.name].join('');

                    request.del({ url: uri, headers: { Authorization: auth2 } }, (e, r, body) => {
                        done();
                    });
                });
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
