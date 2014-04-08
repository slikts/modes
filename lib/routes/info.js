'use strict';

var express = require('express');
var bodyParser = require('body-parser');

var auth = require('../middleware/auth');
var models = require('../models');

var route = express.Router();

function fail() {
  var error = new Error('Invalid credentials');
  error.status = 401;
  error.jsendStatus = 'fail';

  return error;
}

module.exports = function(app) {
  function _info(req, next) {
    var user = req.user;

    next({
      app: {
        version: app.get('version'),
        name: app.get('name'),
      },
      user: {
        token: user._token,
        slug: user._slug
      }
    });
  }

  route
    .route('/')
    .post(bodyParser.urlencoded())
    .post(function(req, res, next) {
      models.User.findOne({
        name: req.body.user
      }).exec(function(err, user) {
        if (!user) {
          return next(err || fail());
        }

        user.comparePassword(req.body.password, function(err, matches) {
          if (matches) {
            req.user = user;

            return _info(req, next);
          }

          next(err || fail());
        });
      });
    })
    .get(auth)
    .get(function(req, res, next) {
      _info(req, next);
    });

  return route;
};