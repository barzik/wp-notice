module.exports = function(grunt) {
    grunt.initConfig({ //object with all tasks.
        phpcs: {
            application: {
                src: ['./public/**/*.php' , './admin/**/*.php']
            },
            options: {
                standard: 'WordPress',
                errorSeverity: 1
            }
        },
        phpcbf: {
            files: {
                src: ['./public/**/*.php' , './admin/**/*.php']
            },
            options: {
                standard: 'WordPress',
                errorSeverity: 1
            }
        },

        phpunit: {
            dir: 'tests/',
            options: {
                configuration: 'phpunit.xml'
            }
        },
        postcss: {
            options: {
                processors: [
                    require('autoprefixer')({browsers: ['last 2 versions', 'Android >= 2.3']}), // add vendor prefixes
                ]
            },
            main: {
                src: 'public/assets/source_css/*.css', //source
                dest: 'public/assets/css/public.css' //destination of compiled css
            }
        },
        jshint: {
            all: ['Gruntfile.js', 'public/**/*.js', 'admin/**/*.js'],
            options : {
                curly : true,
                eqeqeq : true,
                immed : true,
                latedef : true,
                newcap : false,
                noarg : true,
                sub : true,
                undef : true,
                unused : true,
                boss : true,
                eqnull : true,
                browser : true,
                jquery : true,
                globals : {objectL10n : true, module : true, require : true}
            }
        }

    });

    grunt.registerTask('lint', [
        'jshint',
        'phpcs'
    ]);

    grunt.registerTask('test', [
        'jshint',
        'phpcs',
        'phpunit'
    ]);

    grunt.registerTask('build', [
        'postcss',
        'phpcbf',
        'phpunit',
        'jshint',
        'phpcs',
        'phpunit'
    ]);


    grunt.loadNpmTasks('grunt-phpcs');
    grunt.loadNpmTasks('grunt-phpunit');
    grunt.loadNpmTasks('grunt-phpcbf');
    grunt.loadNpmTasks('grunt-postcss');
    grunt.loadNpmTasks('grunt-contrib-jshint');

};
