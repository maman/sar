module.exports = (grunt) ->

  # Variables
  # =========
  pkg   = require './package.json'
  bower = './bower_components'

  # Supercharge
  # ===========
  require('jit-grunt') grunt

  # Configurations
  # ==============
  grunt.initConfig

    # Package
    # =======
    pkg: pkg

    # Notify
    # ======
    notify:
      build:
        options:
          title: 'Build Completed'
          message: pkg.name + 'build finished successfully'

    # Watch
    # =====
    watch:
      less:
        files: ['public/less/{,*/}*.less']
        tasks: ['less:dev']
        options:
          livereload: false

      css:
        files: ['public/css/{,*/}*.css']
        options:
          livereload: true

      js:
        files: ['public/js/{,*/}*.js']
        tasks: ['jshint']
        options:
          livereload: true

      staticFiles:
        files: [
          'public/assets/{,*/}*.{png, jpg, jpeg, gif, svg}'
          'public/{fonts, font-awesome-*}/{,*/}*.{eot, svg, ttf, woff}'
          'src/SAR/{,*/}*.{twig, php}'
        ]
        options:
          livereload: true

    # LESS
    # ====
    less:
      dist:
        options:
          paths: ['public/less']
          cleancss: true
        files: {'public/css/sb-admin-2.css': 'public/less/sb-admin-2.less'}

      dev:
        options:
          paths: ['public/less']
          sourceMap: true
          sourceMapFilename: 'public/css/sb-admin-2.css.map'
          sourceMapBasepath: 'public/css/'
        files: {'public/css/sb-admin-2.css': 'public/less/sb-admin-2.less'}

    # Concat
    # ======
    # concat:
    #   css:

    # CSSMin
    # ======
    cssmin:
      banner: 'Generated on ' + grunt.template.today('mm-dd-yyyy:hhMMss')
      files:
        'public/css/app.min.css': [
          ''
        ]

  grunt.registerTask 'default', ['less:dev']
