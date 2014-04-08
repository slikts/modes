'use strict';

module.exports = function(env) {
  function errorHandler(err, req, res, next) {
    res.statusCode = err.status >= 400 ? err.status : 500;

    var status = {
      status: err._jsendStatus || 'error',
      message: err.message
    };

    if (env === 'development') {
      status.data = {
        stack: err.stack.split('\n')
      };
      console.error(err);
    }

    res.setHeader('Content-Type', 'application/json');
    res.end(JSON.stringify(status, null, 2));
  }

  return errorHandler;
};