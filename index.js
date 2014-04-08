'use strict';

var argv = require('minimist')(process.argv.slice(2));

var pkginfo = require('./package');

var app = require('./lib/app');

app.set('version', pkginfo.version);
app.set('name', pkginfo.name);

if (argv.adduser) {
  app.addUser({
    name: argv.adduser,
    password: argv.adduser,
    role: 'admin'
  });
} else {
  app.run();
}