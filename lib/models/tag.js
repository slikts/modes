'use strict';

var mongoose = require('mongoose');

var slug = require('slug');

var tagSchema = new mongoose.Schema({
  _id: String,
  name: {
    type: String,
    required: true,
    trim: true,
    index: {
      unique: true
    }
  }
});

tagSchema.pre('save', function(next) {
  if (this.isNew) {
    this._id = slug(this.name);
  }
  next();
});

module.exports = {
  tagSchema: tagSchema,
  Tag: mongoose.model('Tag', tagSchema)
};