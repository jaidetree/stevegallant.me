var gulp = require('gulp'),
    compass = require('gulp-compass'),
    livereload = require('gulp-livereload'),
    path = require('path'),
    _base = './';

var paths = {
    'static_dir': _base + 'static/',
    'sass': [_base + 'static/sass/**/*.scss'],
    'css': [_base + 'static/css/**/*.css'],
    'js': [_base + 'static/js/*.js', _base + 'static/js/**/*.js'],
    'templates': [_base + 'views/**/*.php', _base + 'app/views/**/*.php'],
    'assets': [],
    'static_url': function (file) {
        return this.static_dir + file;
    }
};

paths.assets = Array.prototype.concat(paths.css, paths.js, paths.templates);

gulp.task('compass', function () {
    gulp.src(paths.sass)
        .pipe(compass({
            project: path.join(__dirname, 'static'),
            css: 'css',
            image: 'images',
            require: 'susy',
        }))
        .on('error', function (e) {
            console.log(e);
        });
});

gulp.task('watch', function () {
    var server = livereload();
    gulp.watch(paths.assets, function (e) {
        server.changed(e.path);
    });
});

gulp.task('default', ['compass', 'watch']);
