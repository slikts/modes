'use strict';

var express = require('express');
var mongoose = require('mongoose');
var logger = require('morgan');
var objectAssign = require('object-assign');

// Middleware
var responseTime = require('response-time');
var compression = require('compression');
var serveStatic = require('serve-static');

var errorHandler = require('./middleware/error');

var app = express();
var env = app.get('env');
var config = require('./config')(env);

mongoose.connect(config.dbUri);

var models = require('./models');

var modelApiRoute = require('./routes/model');

app
  .use(responseTime())
  .disable('x-powered-by')
  .use(serveStatic(__dirname + '/static', {
    maxAge: config.staticMaxAge
  }))
  .use(logger({
    format: config.logFormat
  }))
  .use(compression());

// app..all(auth(models.User))
app.use('/api', modelApiRoute);

app.use(errorHandler(env));

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