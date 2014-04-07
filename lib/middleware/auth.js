'use strict';

var basicAuth = require('basic-auth');

var unauthorized = new Error('Unauthorized');
unauthorized.status = 401;
unauthorized.jsendStatus = 'fail';

module.exports = function(User) {
  return function auth(req, res, next) {
    var creds = basicAuth(req);

    if (!creds || !creds.name) {
      next(unauthorized);

      return;
    }

    User.findOne({
      name: creds.name
    }).exec(function(err, user) {
      if (!user) {
        next(err || unauthorized);

        return;
      }

      user.comparePassword(creds.pass, function(err, match) {
        if (err) {
          next(err);
        } else if (match) {
          req.user = user;
          next();
        } else {
          next(unauthorized);
        }
      });
    });

  };
};