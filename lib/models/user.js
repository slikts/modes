'use strict';

var mongoose = require('mongoose');

var createdPlugin = require('./plugins/created');
var modifiedPlugin = require('./plugins/modified');

var bcrypt = require('bcrypt');

var userSchema = new mongoose.Schema({
  name: {
    type: String,
    match: /^\w+$/,
    required: true,
    index: {
      unique: true
    }
  },
  email: {
    type: String,
    index: true,
    match: /^\S+@\S+\.\w{2,4}$/
  },
  role: {
    type: String,
    enum: ['admin', 'user'],
    default: 'user'
  },
  password: {
    type: String,
    required: true,
    trim: true
  }
}).method({
  hashPassword: function(next) {
    var self = this;

    bcrypt.genSalt(10, function(err, salt) {
      bcrypt.hash(self.password, salt, function(err, hash) {
        if (hash) {
          self.password = hash;
        }

        next(err);
      });
    });

    return this;
  },
  comparePassword: function(pass, next) {
    bcrypt.compare(pass, this.password, next);

    return this;
  }
});

userSchema.pre('save', function(next) {
  if (this.isNew || this.isModified('password')) {
    this.hashPassword(next);
  } else {
    next();
  }
});

userSchema.plugin(createdPlugin, 'User');
userSchema.plugin(modifiedPlugin, 'User');

module.exports = {
  userSchema: userSchema,
  User: mongoose.model('User', userSchema)
};