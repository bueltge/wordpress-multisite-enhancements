/* jshint node:true */
module.exports = function( grunt ) {
	'use strict';

	var _ = require( 'lodash' );

	var configObject = {
		config: {
			assets: {
				src: 'assets',
				dest: 'assets'
			},
			dest: 'src',
			scripts: {
				src: 'assets/js/',
				dest: 'assets/js/'
			},
			resources: {
				src: 'resources/',
				dest: 'resources/'
			}
		},

		concat: {
			options: {
				separator: '\n'
			}
		},

		imagemin: {
			options: {
				optimizationLevel: 7
			},
			resources: {
				expand: true,
				cwd: '<%= config.resources.src %>',
				src: [ '**/*.{gif,jpeg,jpg,png}' ],
				dest: '<%= config.resources.dest %>'
			}
		},

		jscs: {
			options: {
				config: true
			},
			grunt: {
				src: [ 'Gruntfile.js' ]
			},
			scripts: {
				expand: true,
				cwd: '<%= config.scripts.src %>',
				src: [ '**/*.js', '!**/*.min.js' ]
			}
		},

		jshint: {
			options: {
				jshintrc: true,
				reporter: require( 'jshint-stylish' )
			},
			grunt: {
				src: [ 'Gruntfile.js' ]
			},
			scripts: {
				src: [ '<%= config.scripts.src %>**/*.js' ]
			}
		},

		jsonlint: {
			configs: {
				src: [ '.{jscs,jshint}rc' ]
			},
			json: {
				src: [ '*.json' ]
			}
		},

		jsvalidate: {
			options: {
				globals: {},
				esprimaOptions: {},
				verbose: false
			},
			src: {
				src: [ '<%= config.scripts.src %>**/*.js' ]
			},
			dest: {
				src: [ '<%= config.scripts.dest %>**/*.js' ]
			}
		},

		lineending: {
			options: {
				eol: 'lf',
				overwrite: true
			},
			grunt: {
				src: [ 'Gruntfile.js' ]
			},
			scripts: {
				src: [ '<%= config.scripts.dest %>*.js' ],
				dest: '<%= config.scripts.dest %>'
			},
			styles: {
				src: [ '<%= config.styles.dest %>*.css' ],
				dest: '<%= config.styles.dest %>'
			}
		},

		uglify: {
			options: {
				ASCIIOnly: true
			},
			scripts: {
				expand: true,
				cwd: '<%= config.scripts.dest %>',
				src: [ '*.js', '!*.min.js' ],
				dest: '<%= config.scripts.dest %>',
				ext: '.min.js'
			}
		},

		makepot: {
			target: {
				options: {
					include: [
						'multisite-enhancements.php',
						'uninstall.php',
						'src/.*'
					],
					type: 'wp-plugin'
				}
			}
		},

		watch: {
			options: {
				dot: true,
				spawn: true,
				interval: 2000
			},

			assets: {
				files: [ '<%= config.resources.src %>*.{gif,jpeg,jpg,png}' ],
				tasks: [
					'newer:imagemin:resources'
				]
			},

			configs: {
				files: [ '.{jscs,jshint}rc' ],
				tasks: [
					'jsonlint:configs'
				]
			},

			grunt: {
				files: [ 'Gruntfile.js' ],
				tasks: [
					'jscs:grunt',
					'jshint:grunt',
					'lineending:grunt'
				]
			},

			json: {
				files: [ '*.json' ],
				tasks: [
					'jsonlint:json'
				]
			},

			scripts: {
				files: [ '<%= config.scripts.src %>**/*.js' ],
				tasks: [
					'jsvalidate:src',
					'jshint:force',
					'jscs:force',
					'newer:concat',
					'newer:lineending:scripts',
					'newer:uglify',
					'jsvalidate:dest'
				]
			}
		},

		compress: {
			zip: {
				options: {
					archive: 'multisite-enhancements.zip'
				},
				files: [
					{ src: [ 'multisite-enhancements.php' ], dest: '/multisite-enhancements/', filter: 'isFile' },
					{ src: [ 'readme.txt' ], dest: '/multisite-enhancements/', filter: 'isFile' },
					{ src: [ 'LICENSE' ], dest: '/multisite-enhancements/', filter: 'isFile' },
					{ src: [ 'src/**' ], dest: '/multisite-enhancements/' },
					{ src: [ 'assets/**' ], dest: '/multisite-enhancements/' },
					{ src: [ 'vendor/**' ], dest: '/multisite-enhancements/' }
				]
			}
		},

		run: {
			composer_no_dev: {
				cmd: 'composer',
				args: [
					'install',
					'--prefer-dist',
					'--no-dev'
				]
			},
			composer_with_dev: {
				cmd: 'composer',
				args: [
					'install'
				]
			}
		}
	};

	// Add development target for JSCS.
	configObject.jscs.force = _.merge(
		{},
		configObject.jscs.scripts,
		{
			options: {
				force: true
			}
		}
	);

	// Add development target for JSHint.
	configObject.jshint.force = _.merge(
		{},
		configObject.jshint.scripts,
		{
			options: {
				devel: true,
				force: true
			}
		}
	);

	require( 'load-grunt-tasks' )( grunt );

	grunt.initConfig( configObject );

	grunt.registerTask( 'assets', configObject.watch.assets.tasks );

	grunt.registerTask( 'configs', configObject.watch.configs.tasks );

	grunt.registerTask( 'grunt', configObject.watch.grunt.tasks );

	grunt.registerTask( 'json', configObject.watch.json.tasks );

	grunt.registerTask( 'scripts', [
		'jsvalidate:src',
		'jshint:scripts',
		'jscs:scripts',
		'newer:concat',
		'newer:lineending:scripts',
		'newer:uglify',
		'jsvalidate:dest'
	] );

	grunt.registerTask( 'forcescripts', configObject.watch.scripts.tasks );

	grunt.registerTask( 'lint', [
		'jshint'
	] );

	grunt.registerTask( 'precommit', [
		'assets',
		'configs',
		'grunt',
		'json',
		'scripts'
	] );

	grunt.registerTask( 'default', [
		'configs',
		'grunt',
		'json',
		'forcescripts'
	] );

	grunt.registerTask( 'grunt-wp-i18n', [
		'makepot'
	] );

	grunt.registerTask( 'package', [
		'precommit',
		'run:composer_no_dev',
		'compress:zip',
		'run:composer_with_dev'
	] );

	// Delegation task for grunt-newer to check files different from the individual task's files.
	grunt.registerTask( 'delegate', function() {
		grunt.task.run( this.args.join( ':' ) );
	} );
};
