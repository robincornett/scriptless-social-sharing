'use strict';

var gulp = require('gulp'),
    notify = require('gulp-notify'),
    sass = require('gulp-sass'),
    autoprefixer = require('gulp-autoprefixer');

gulp.task('sass', function () {
  gulp.src('includes/*.scss')
    .pipe(sass({outputStyle: 'expanded'}).on('error', sass.logError))
    .pipe(autoprefixer({
        browsers: ['last 5 versions'],
        cascade: false
    }))
    .pipe(gulp.dest('includes/css'))
    .pipe(notify({ message: 'Your sass is fine.' }));
});

gulp.task('watch', function(){
    gulp.watch('includes/*.scss', ['sass']);
});

gulp.task('default', ['sass', 'watch']);
