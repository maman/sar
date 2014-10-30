module.exports = (grunt) ->

  # Variables
  # =========
  pkg   = require './package.json'
  bower = './bower_components/'

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
          paths: [
            bower + 'bootstrap/less'
            bower + 'font-awesome/less'
            bower + 'morrisjs/less'
            'public/less'
          ]
          compress: true
          cleancss: true
          cleancssOptions:
            keepSpecialComments: 0
            keepBreaks: false
          ieCompat: true
          report: 'gzip'
        files: {'public/css/tmp/sb-admin-2.css': 'public/less/sb-admin-2.less'}

      dev:
        options:
          paths: [
            bower + 'bootstrap/less'
            bower + 'font-awesome/less'
            bower + 'morrisjs/less'
            'public/less'
          ]
          sourceMap: true
          sourceMapFilename: 'public/css/app.min.css.map'
          sourceMapBasepath: 'public/css/'
          sourceMapRootPath: '/less/'
        files: {'public/css/app.min.css': 'public/less/sb-admin-2.less'}

    # Concat
    # ======
    concat:
      css:
        src: [
          bower + 'datatables-bootstrap3/BS3/assets/css/datatables.css'
          'public/css/plugins/*.css'
          'public/css/tmp/sb-admin-2.css'
        ]
        dest: 'public/css/tmp/app.css'

    # CSSMin
    # ======
    cssmin:
      dist:
        options:
          banner: '/* Style definition for ' + pkg.name + '#' + pkg.version + '\n Generated on ' + grunt.template.today('mm-dd-yyyy:hhMMss') + ' */'
          keepSpecialComments: '0'
          report: 'gzip'
        files:
          'public/css/app.min.css': 'public/css/tmp/app.css'

    # Clean
    # =====
    clean:
      dist:
        files: [
          dot: true
          src: [
            'public/css/tmp'
            '!public/css/tmp/.git*'
          ]
        ]

  grunt.registerTask 'default', ['less:dev']
