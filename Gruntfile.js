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
        }
    });
    grunt.loadNpmTasks('grunt-phpcs');
    grunt.loadNpmTasks('grunt-phpunit');
    grunt.loadNpmTasks('grunt-phpcbf');
};
