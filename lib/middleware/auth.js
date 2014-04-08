'use strict';

var cache = {};

function unauthorized() {
  var error = new Error('Unauthorized');
  error.status = 401;
  error._jsendStatus = 'fail';

  return error;
}

var pattern = /Token (\S+):(\S+)/;

function _getCreds(header) {
  var creds;

  if (header) {
    header = header.match(pattern);

    if (header) {
      creds = {
        token: header.pop(),
        user: decodeURIComponent(new Buffer(header.pop(), 'base64'))
      };
    }
  }

  return creds;
}

function auth(req, res, next) {
  var creds = _getCreds(req.headers.authorization);

  if (!creds || !creds.user) {
    return next(unauthorized());
  }

  cache.User.findOne({
    name: creds.user
  }).exec(function(err, user) {
    if (user && user._token === creds.token) {
      req.user = user;

      return next();
    }

    next(err || unauthorized());
  });
}

auth.init = function(User) {
  cache.User = User;
};

module.exports = auth;