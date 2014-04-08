'use strict';

var express = require('express');
var bodyParser = require('body-parser');

var auth = require('../middleware/auth');
var models = require('../models');

var route = express.Router();

function _applyCommands(target, commands, next) {
  function callback(err, data) {
    next(err || data);
  }

  if (!Array.isArray(commands)) {
    return next(new TypeError('Commands must be an array'));
  }

  commands.every(function(step) {
    var key = Object.keys(step)[0];
    if (!Array.isArray(step[key])) {
      next(new TypeError('Command arguments must be an array'));

      return false;
    }

    var args = step[key].map(function(arg) {
      if (arg === '__fn') {
        arg = callback;
      }

      return arg;
    });
    var method = target[key];
    if (!method) {
      next(new TypeError('Invalid method name'));

      return false;
    }

    target = method.apply(target, args);

    return true;
  });
}

route
  .use(auth)
  .param('model', function(req, res, next, id) {
    req.model = models[id];

    if (!req.model) {
      return next(new Error('Model not found'));
    }

    next();
  })
  .route('/:model')
  .post(bodyParser.json())
  .post(function(req, res, next) {
    _applyCommands(req.model, req.body, next);
  });

module.exports = route;