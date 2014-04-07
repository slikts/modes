'use strict';

var express = require('express');
var mongoose = require('mongoose');
var objectAssign = require('object-assign');

// Middleware
var responseTime = require('response-time');
var compression = require('compression');
var errorHandler = require('./middleware/error');
var auth = require('./middleware/auth');

var app = express();
var env = app.get('env');
var config = require('./config')(env);

mongoose.connect(config.dbUri);

// console.log(db);

var models = require('./models');

app
  .use(compression())
  .use(responseTime())
  .use(auth(models.User))
  .use(errorHandler(env));

app.get('/', function(req, res) {
  res.send('hello world');
});

// rest.get('/q', function(req, res, next) {
//   next(new Error('poo'));
// });

// rest.get({
//   path: '/list/:model',
//   protector: only('admin')
// }, function(req, res, next) {
//   models[req.params.model].find()
//     .populate('createdBy modifiedBy', 'name')
//     .exec(function(err, items) {
//       next(err, items);
//     });
// });

// rest.post({
//   path: '/add/:model',
//   protector: only('admin')
// }, function(req, res, next) {
//   var doc = new models[req.params.model](req.body);

//   doc._user = req.user;

//   doc.save(function(err, x) {
//     next(err, x);
//   });
// });

// rest.post({
//   path: '/update/:model/:id',
//   protector: only('admin')
// }, function(req, res, next) {
//   models[req.params.model].find();
//   next();
// });

// app
//   .use(connect.responseTime())
//   .use(auth)
//   .use(connect.json())
// .use(rest.rester())
// .use(errorHandler);

module.exports = {
  run: function() {
    console.log('running %s modes at port %s', app.get('env'), config.restApiPort);

    app.listen(config.restApiPort);
  },
  addUser: function(doc) {
    console.log('creating user `%s` (role: %s)', doc.name, doc.role);

    var user = models.User(doc);

    objectAssign(user, {
      createdBy: user,
      modifiedBy: user
    });

    user.save(function(err) {
      if (err) {
        throw err;
      }
      process.exit();
    });
  }
};