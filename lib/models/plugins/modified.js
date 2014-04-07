'use strict';

var mongoose = require('mongoose');

module.exports = function modifiedPlugin(schema) {
  schema.add({
    modified: Date,
    modifiedBy: {
      required: true,
      type: mongoose.Schema.ObjectId,
      ref: 'User'
    }
  });

  schema.pre('save', function(next) {
    this.modified = new Date();
    next();
  });

  schema.pre('validate', function(next) {
    if (!this.modifiedBy && this._user) {
      this.modifiedBy = this._user;
    }
    next();
  });
};