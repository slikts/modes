'use strict';

var mongoose = require('mongoose');

module.exports = function modifiedPlugin(schema) {
  schema.add({
    modified: Date,
    modifiedBy: {
      type: mongoose.Schema.ObjectId,
      ref: 'User'
    }
  });

  schema.pre('save', function(next, callback, user) {
    if (!this.isNew && this.modifiedPaths().length) {
      this.modified = new Date();
      this.modifiedBy = user;
    }
    next(callback);
  });
};