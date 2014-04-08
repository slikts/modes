'use strict';

var objectAssign = require('object-assign');

module.exports = function(env) {
  var config;

  try {
    config = require('./' + env);
  } catch (err) {
    config = {};
  }

  return objectAssign(require('./defaults'), config);
};