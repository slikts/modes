'use strict';

var express = require('express');
var jsonBody = require('body/json');

var auth = require('../middleware/auth');
var models = require('../models');

var route = express.Router();


route
  .use(auth(models.User))
  .param('model', function(req, res, next, id) {
    req.model = models[id];

    if (!req.model) {
      next(new Error('Invalid model name'));

      return;
    }

    next();
  })
  .route('/:model')
  .get(function(req, res, next) {
    req.model.find().exec(function(err, data) {
      next(err || data);
    });
  })
  .all(function(req, res, next) {
    jsonBody(req, res, function(err, body) {
      if (err) {
        next(err);

        return;
      }

      req.body = body;
      next();
    });
  })
  .patch(function(req, res, next) {
    next({
      patch: 1
    });
  })
  .put(function(req, res, next) {
    //var doc = new req.model(req.body);

    next({
      put: 1
    });
  })
  .post(function(req, res, next) {
    var model = new req.model(req.body);

    model._user = req.user;

    model.save(function(err, doc) {
      if (err) {
        return next(err);
      }

      next(doc);
    }, req.user, function() {});
  })
  .all(function(data, req, res, next) {
    if (data instanceof Error) {
      return next(data);
    }

    res.send({
      status: 'success',
      data: data
    });
  });

module.exports = route;