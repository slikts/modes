'use strict';

module.exports = function(data, req, res, next) {
  if (data instanceof Error) {
    return next(data);
  }

  res.send({
    status: 'success',
    data: data
  });
};