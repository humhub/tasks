module.exports = function (grunt) {

    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        sass: {
            options: {
                implementation: require('sass')
            },
            dev: {
                files: {
                    'resources/css/tasks.css': 'resources/css/tasks.scss'
                }
            }
        },
        cssmin: {
            target: {
                files: {
                    'resources/css/tasks.min.css': ['resources/css/tasks.css']
                }
            }
        },
    });

    grunt.loadNpmTasks('grunt-sass');
    grunt.loadNpmTasks('grunt-contrib-cssmin');

    grunt.registerTask('build', ['sass', 'cssmin']);
};
