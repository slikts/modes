'use strict';

var mongoose = require('mongoose');

module.exports = function createdPlugin(schema) {
  schema.add({
    created: {
      type: Date,
      default: Date.now
    },
    createdBy: {
      required: true,
      type: mongoose.Schema.ObjectId,
      ref: 'User'
    }
  });

  schema.pre('validate', function(next) {
    if (!this.createdBy && this._user) {
      this.createdBy = this._user;
    }
    next();
  });
};