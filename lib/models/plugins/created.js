'use strict';

var mongoose = require('mongoose');

module.exports = function createdPlugin(schema) {
  schema.add({
    created: {
      type: Date,
      default: Date.now
    },
    createdBy: {
      type: mongoose.Schema.ObjectId,
      ref: 'User'
    }
  });

  schema.pre('save', function(next, callback, user) {
    if (this.isNew && !this.createdBy) {
      if (!user) {
        return next(new Error('User is required'));
      }

      this.createdBy = user;
    }
    next(callback);
  });
};