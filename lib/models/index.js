'use strict';

var fs = require('fs');
var objectAssign = require('object-assign');

fs.readdirSync(__dirname).forEach(function(file) {
  if (file !== 'index.js' && file.match(/\.js$/)) {
    objectAssign(module.exports, require('./' + file));
  }
});