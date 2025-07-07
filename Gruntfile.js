module.exports = function (grunt) {

    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        sass: {
            options: {
                implementation: require('sass')
            },
            dev: {
                files: {
                    'resources/css/task.css': 'resources/css/task.scss'
                }
            }
        },
        cssmin: {
            target: {
                files: {
                    'resources/css/task.min.css': ['resources/css/task.css']
                }
            }
        },
    });

    grunt.loadNpmTasks('grunt-sass');
    grunt.loadNpmTasks('grunt-contrib-cssmin');

    grunt.registerTask('build', ['sass', 'cssmin']);
};
