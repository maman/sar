module.exports = (grunt) ->

  # Variables
  # =========
  pkg   = require './package.json'
  bower = './bower_components/'
  namespace = './src/SAR/'

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
        tasks: ['less:dev', 'concat:dev']
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
          namespace + '**/*.{php, router.php}'
          namespace + '**/*.twig'
          '!' + namespace + 'templates/cache/**/*.php'
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
        files: {'public/css/tmp/app.dist.css': 'public/less/sb-admin-2.less'}

      dev:
        options:
          paths: [
            bower + 'bootstrap/less'
            bower + 'font-awesome/less'
            bower + 'morrisjs/less'
            'public/less'
          ]
          sourceMap: true
          sourceMapFilename: 'public/css/tmp/app.dev.css.map'
          sourceMapBasepath: 'public/css/tmp/'
          sourceMapRootPath: '/less/'
        files: {'public/css/tmp/app.dev.css': 'public/less/sb-admin-2.less'}

    # Concat
    # ======
    concat:
      dist:
        src: [
          'public/css/tmp/app.dist.css'
          bower + 'datatables-bootstrap3/BS3/assets/css/datatables.css'
          bower + 'metisMenu/dist/metisMenu.min.css'
          'public/css/plugins/*.css'
        ]
        dest: 'public/css/tmp/app.dist.css'

      dev:
        src: [
          'public/css/tmp/app.dev.css'
          bower + 'datatables-bootstrap3/BS3/assets/css/datatables.css'
          bower + 'metisMenu/dist/metisMenu.min.css'
          'public/css/plugins/*.css'
        ]
        dest: 'public/css/app.min.css'

    # CSSMin
    # ======
    cssmin:
      dist:
        options:
          banner: '/* Style definition for ' + pkg.name + '#' + pkg.version + '\n Generated on ' + grunt.template.today('mm-dd-yyyy:hhMMss') + ' */'
          keepSpecialComments: '0'
          report: 'gzip'
        files:
          'public/css/app.min.css': 'public/css/tmp/app.dist.css'

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
  grunt.registerTask 'work', [
    'clean:dist'
    'less:dev'
    'concat:dev'
    'watch'
  ]
  grunt.registerTask 'build', [
    'clean:dist'
    'less:dist'
    'concat:dist'
    'cssmin:dist'
  ]
