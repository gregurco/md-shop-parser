const gulp = require('gulp');
const sourcemaps = require('gulp-sourcemaps');
const del = require("del");

var paths = {
    source: {
        js: [
            'vendor/bower_components/bootstrap/dist/js/bootstrap.min.js',
            'vendor/bower_components/jquery/dist/jquery.min.js',
            'vendor/bower_components/typeahead.js/dist/typeahead.bundle.js',
            'vendor/bower_components/handlebars/handlebars.js',
            'vendor/bower_components/moment/min/moment.min.js',
            'vendor/bower_components/chart.js/dist/Chart.min.js'
        ],
        css: [
            'vendor/bower_components/bootstrap/dist/css/bootstrap.min.css',
            'vendor/bower_components/font-awesome/css/font-awesome.min.css'
        ],
        fonts: [
            'vendor/bower_components/bootstrap/dist/fonts/glyphicons-halflings-regular.*',
            'vendor/bower_components/font-awesome/fonts/*'
        ]
    },
    dist: {
        js: 'web/assets/vendor/js',
        css: 'web/assets/vendor/css',
        fonts: 'web/assets/vendor/fonts'
    }
};

/* Clean generated files */
gulp.task('clean', function() {
    return del([
        paths.dist.css + '/*.*',
        paths.dist.fonts + '/*.*',
        paths.dist.js + '/*.*'
    ]);
});

/* Copy vendor files */
gulp.task('copy', function () {
    gulp.src(paths.source.js)
        .pipe(gulp.dest(paths.dist.js));

    gulp.src(paths.source.css)
        .pipe(gulp.dest(paths.dist.css));

    gulp.src(paths.source.fonts)
        .pipe(gulp.dest(paths.dist.fonts));
});

gulp.task('default', ['clean'], function() {
    gulp.start('copy');
});