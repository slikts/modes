'use strict';

var objectAssign = require('object-assign');

module.exports = function(env) {
  return objectAssign(require('./defaults'), require('./' + env));
};