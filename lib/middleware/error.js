'use strict';

var objectAssign = require('object-assign');

module.exports = function(env) {
  function errorHandler(err, req, res, next) {
    if (err.status) {
      res.statusCode = err.status >= 400 ? err.status : 500;
    }

    if (env === 'development') {
      console.error(err);
    }

    // var error = {
    //   message: err.message,
    //   stack: err.stack
    // };


    res.setHeader('Content-Type', 'application/json');
    res.end(JSON.stringify({
      error: objectAssign({}, err)
    }));
  }

  return errorHandler;
};