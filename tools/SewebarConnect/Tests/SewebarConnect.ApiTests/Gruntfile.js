module.exports = function(grunt) {
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        typescript: {
            base: {
                src: ['test/*.ts'],
                // dest: './',
                options: {
                    module: 'commonjs', //or commonjs
                    target: 'es5', //or es3
                    // base_path: 'path/to/typescript/files',
                    sourcemap: true,
                    declaration: false
                }
            }
        },
        watch: {
            files: ['<%= typescript.base.src %>'],
            tasks: ['typescript']
        }
    });

    grunt.loadNpmTasks('grunt-typescript');
    // grunt.loadNpmTasks('grunt-contrib-uglify');
    // grunt.loadNpmTasks('grunt-contrib-jshint');
    // grunt.loadNpmTasks('grunt-contrib-qunit');
    grunt.loadNpmTasks('grunt-contrib-watch');
    // grunt.loadNpmTasks('grunt-contrib-concat');

    // grunt.registerTask('test', ['jshint', 'qunit']);

    grunt.registerTask('default', ['typescript']);
};