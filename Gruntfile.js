'use strict';

module.exports = function(grunt) {

  grunt.initConfig({
    pkg: grunt.file.readJSON('package.json'),
    watch: {
      scripts: {
        files: 'index.js',
        tasks: ['forever:server1:restart'],
        options: {
          interrupt: true,
          atBegin: true,
        },
      },
    },
    forever: {
      server1: {
        options: {
          index: 'index.js',
          logDir: 'logs'
        }
      },
    },
  });

  grunt.loadNpmTasks('grunt-contrib-watch');
  grunt.loadNpmTasks('grunt-forever');
};