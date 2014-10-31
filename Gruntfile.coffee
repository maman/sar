module.exports = (grunt) ->

  # Variables
  # =========
  pkg          = require './package.json'
  webpack      = require('webpack')
  webpackBower = require('bower-webpack-plugin')
  bower        = './bower_components/'
  namespace    = './src/SAR/'

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
          title: pkg.name + '#' + pkg.version
          message: 'Build finished successfully'

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
        files: ['public/js/main.js']
        tasks: ['jshint', 'webpack:dev']
        options:
          livereload: true

      staticFiles:
        files: [
          'public/assets/{,*/}*.{png, jpg, jpeg, gif, svg}'
          'bootstrap/*.php'
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
        files: {'public/css/tmp/app.dev.css': 'public/less/sb-admin-2.less'}

    # JSHint
    # ======
    jshint:
      options:
        maxerr: 15
        indent: 2
        quotmark: true
        globalstrict: true
        browser: true
        browserify: true
        jquery: true
        reporter: require('jshint-stylish')
      all: [
        'public/js/**/*.js'
        '!public/js/bundle.js'
      ]

    # Webpack
    # =======
    webpack:
      options:
        entry: './public/js/main.js'
        output:
          path: './public/js/'
          filename: 'bundle.js'
        plugins: [
          new webpackBower(
            excludes: /.*\.(less|css|woff|svg|ttf|eot)([\?]?.*)$/
          )
          new webpack.ProvidePlugin(
            $: 'jquery'
            jquery: 'jquery'
            jQuery: 'jquery'
            'windows.jquery': 'jquery'
            'windows.jQuery': 'jquery'
          )
        ]
      stats: false
      dist:
        plugins: [
          new webpack.DefinePlugin(
            'process.env': NODE_ENV: JSON.stringify('production')
          )
          new webpack.optimize.DedupePlugin()
          new webpack.optimize.UglifyJsPlugin()
        ]
      dev:
        devtool: 'sourcemap'
        debug: true

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
            'public/css/app.min.css'
            'public/js/*.js'
            'public/js/*.js.map'
            '!public/js/main.js'
            '!public/js/.git*'
          ]
        ]

    # Bower
    # =====
    'bower-install-simple':
      options:
        color: true

      dist:
        options:
          production: true

      dev:
        options:
          production: false

    # Copy
    # ====
    copy:
      font:
        files: [
          expand: true
          flatten: true
          src: bower + 'font-awesome/fonts/*'
          dest: 'public/fonts/'
          filter: 'isFile'
        ]

    # Bump
    # ====
    bump:
      options:
        files: [
          'package.json'
          'composer.json'
          'bower.json'
        ]
        commit: true
        commitMessage: 'Release v%VERSION%'
        commitFiles: ['-a']
        push: false

  # Register tasks
  # ==============
  grunt.registerTask 'default', ['work']
  grunt.registerTask 'prepare', [
    'clean:dist'
    'bower-install-simple'
    'copy:font'
  ]
  grunt.registerTask 'work', [
    'clean:dist'
    'less:dev'
    'concat:dev'
    'webpack:dev'
    'watch'
  ]
  grunt.registerTask 'build', [
    'clean:dist'
    'less:dist'
    'concat:dist'
    'cssmin:dist'
    'jshint:all'
    'webpack:dist'
    'bump:patch'
    'notify:build'
  ]
