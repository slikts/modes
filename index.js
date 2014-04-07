'use strict';

var argv = require('minimist')(process.argv.slice(2));

var app = require('./lib/app');

if (argv.adduser) {
  app.addUser({
    name: argv.adduser,
    password: argv.adduser,
    role: 'admin'
  });
} else {
  app.run();
}