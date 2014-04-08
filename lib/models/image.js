'use strict';

var mongoose = require('mongoose');
var autoIncrement = require('mongoose-auto-increment');

var createdPlugin = require('./plugins/created');
var modifiedPlugin = require('./plugins/modified');

var variantSchema, imageSchema;

autoIncrement.initialize(mongoose);

variantSchema = new mongoose.Schema({
  width: Number,
  height: Number,
  fileName: String,
  path: String,
  size: Number,
  md5: {
    type: String,
    index: {
      unique: true
    }
  }
});

variantSchema.plugin(createdPlugin, {
  user: false
});

imageSchema = new mongoose.Schema({
  variants: [variantSchema],
  tags: [{
    type: String,
    ref: 'Tag',
    index: {
      unique: true
    }
  }],
  meta: {}
}).method({
  getShortName: function() {
    return this._id.toString(32);
  }
});

imageSchema.plugin(autoIncrement.plugin, 'Image');
imageSchema.plugin(createdPlugin, 'Image');
imageSchema.plugin(modifiedPlugin, 'Image');

module.exports = {
  variantSchema: variantSchema,
  imageSchema: imageSchema,
  Image: mongoose.model('Image', imageSchema)
};