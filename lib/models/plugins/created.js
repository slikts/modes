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
      this.createdBy = user;
    }
    next(callback);
  });
};